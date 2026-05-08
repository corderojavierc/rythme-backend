# Documentación de Nuevas Funcionalidades: Rankings y Snapshots

Esta documentación explica las nuevas funcionalidades implementadas en el proyecto Laravel para manejar rankings de música. Está diseñada para desarrolladores junior, explicando paso a paso qué hace cada componente, por qué existe y cómo funciona, con ejemplos de código.

## Introducción

El sistema de rankings permite mostrar las mejores músicas basadas en calificaciones de usuarios. Hay dos tipos de rankings:

1. **Top Rated**: Músicas con la mejor calificación promedio.
2. **Most Rated**: Músicas con más calificaciones (más votadas).

Los rankings se calculan mensualmente y se almacenan en caché para mejorar el rendimiento. También se guardan snapshots históricos para consultar rankings pasados.

## 1. RankingController

El `RankingController` es un controlador API que maneja las rutas para obtener rankings actuales e históricos.

### Ubicación
`app/Http/Controllers/Api/RankingController.php`

### Funciones Principales

#### topRated()
Devuelve el ranking de músicas mejor calificadas del mes actual.

```php
public function topRated(): JsonResponse
{
    $period = now()->format('Y-m'); // Ejemplo: '2025-05'
    $from = now()->startOfMonth()->startOfDay();
    $to = now()->endOfDay();

    $rankings = Cache::remember(
        key: 'rankings:top-rated:'.$period,
        ttl: now()->addHours(6), // Cache por 6 horas
        callback: fn (): Collection => Post::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
            ->groupBy('music_id')
            ->orderByDesc('rating') // Ordena por rating descendente
            ->with('music') // Carga la relación con Music
            ->limit(self::TOP) // Top 10
            ->get()
            ->map(fn (Post $item, int $i): Post => $item->setAttribute('position', $i + 1))
    );

    return response()->json([
        'period' => $period,
        'data' => RankingResource::collection($rankings),
    ]);
}
```

**Explicación:**
- Calcula el período actual (año-mes).
- Busca posts del mes actual.
- Agrupa por `music_id`, calcula promedio de rating y cuenta de ratings.
- Ordena por rating promedio descendente.
- Limita a 10 resultados.
- Agrega posición (1, 2, 3...).
- Usa caché para no recalcular cada vez.
- Devuelve JSON con período y datos formateados por `RankingResource`.

#### mostRated()
Similar a `topRated()`, pero ordena por cantidad de ratings primero, luego por rating promedio.

```php
->orderByDesc('count_ratings')
->orderByDesc('rating')
```

#### topRatedHistory($period) y mostRatedHistory($period)
Para rankings históricos.

```php
public function topRatedHistory(string $period): JsonResponse
{
    if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
        return response()->json([
            'message' => 'Formato inválido. Usa YYYY-MM, ejemplo: 2025-04',
        ], 422);
    }

    if ($period === now()->format('Y-m')) {
        return $this->topRated(); // Si es el mes actual, usa el método actual
    }

    $date = now()->createFromFormat('Y-m', $period)->startOfMonth();

    $rankings = Cache::rememberForever( // Cache permanente
        key: 'rankings:top-rated:history:'.$period,
        callback: fn (): Collection => TopRatedMusic::query()
            ->with('music')
            ->where('period', $date->toDateString())
            ->orderBy('rank_position')
            ->get()
    );

    return response()->json([
        'period' => $period,
        'data' => RankingResource::collection($rankings),
    ]);
}
```

**Explicación:**
- Valida el formato del período (YYYY-MM).
- Si es el mes actual, redirige a `topRated()`.
- Sino, consulta la tabla `TopRatedMusic` para el período histórico.
- Cache permanente porque los datos históricos no cambian.

## 2. RankingResource

Transforma los datos del ranking en JSON estructurado para la API.

### Ubicación
`app/Http/Resources/RankingResource.php`

### Código Principal

```php
final class RankingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->rank_position ?? $this->position, // Posición en ranking
            'rating' => round((float) $this->rating, 2), // Rating promedio redondeado
            'count_ratings' => (int) $this->count_ratings, // Cantidad de ratings
            'music' => [
                'id' => $this->music->id,
                'title' => $this->music->title,
                'artist' => $this->music->artist,
                'cover_url' => $this->music->cover_url,
            ],
        ];
    }
}
```

**Explicación:**
- `position`: Viene de `rank_position` (para históricos) o `position` (calculado dinámicamente).
- `rating`: Promedio redondeado a 2 decimales.
- `count_ratings`: Número de calificaciones.
- `music`: Datos básicos de la música relacionada.

Esto asegura que la API siempre devuelva datos consistentes.

## 3. SnapshotMonthlyRankingJob

Job que se ejecuta mensualmente para guardar snapshots de rankings históricos.

### Ubicación
`app/Jobs/SnapshotMonthlyRankingJob.php`

### Propósito
Guardar los rankings del mes anterior en tablas dedicadas (`TopRatedMusic`, `MostValoratedMusic`) para consultas históricas.

### Código Principal

```php
final class SnapshotMonthlyRankingJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3; // Reintenta hasta 3 veces si falla
    public int $timeout = 60; // Timeout de 60 segundos

    public function __construct(
        private readonly ?CarbonInterface $snapshotMonth = null,
    ) {}

    public function uniqueId(): string
    {
        return $this->resolveMonth()->format('Y-m'); // ID único por mes
    }

    public function handle(): void
    {
        $month = $this->resolveMonth(); // Mes anterior por defecto
        $from = $month->copy()->startOfMonth()->startOfDay();
        $to = $month->copy()->endOfMonth()->endOfDay();
        $period = $from->toDateString();

        DB::transaction(function () use ($from, $to, $period): void {
            $this->snapshotTopRated($from, $to, $period);
            $this->snapshotMostRated($from, $to, $period);
        });

        // Limpia caché para forzar recálculo
        $cacheKey = $month->format('Y-m');
        Cache::forget('rankings:top-rated:'.$cacheKey);
        Cache::forget('rankings:most-rated:'.$cacheKey);
        Cache::forget('rankings:top-rated:history:'.$cacheKey);
        Cache::forget('rankings:most-rated:history:'.$cacheKey);
    }

    private function resolveMonth(): CarbonInterface
    {
        return $this->snapshotMonth ?? now()->subMonth(); // Mes anterior
    }
}
```

**Explicación:**
- `ShouldBeUnique`: Evita que se ejecute múltiples veces para el mismo mes.
- `ShouldQueue`: Se ejecuta en cola para no bloquear.
- Calcula rankings del mes anterior.
- Usa transacción para asegurar consistencia.
- Limpia caché para que las consultas usen datos frescos.

#### snapshotTopRated()
```php
private function snapshotTopRated(CarbonInterface $from, CarbonInterface $to, string $period): void
{
    $rows = Post::query()
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
        ->groupBy('music_id')
        ->orderByDesc('rating')
        ->limit(10)
        ->get()
        ->map(fn (Post $item, int $i): array => [
            'period' => $period,
            'rank_position' => $i + 1,
            'music_id' => $item->music_id,
            'rating' => round((float) $item->rating, 2),
            'count_ratings' => (int) $item->count_ratings,
        ])
        ->all();

    if (! empty($rows)) {
        TopRatedMusic::query()->upsert(
            $rows,
            uniqueBy: ['period', 'rank_position'], // Claves únicas
            update: ['music_id', 'rating', 'count_ratings'] // Campos a actualizar
        );
    }
}
```

**Explicación:**
- Calcula top 10 por rating promedio.
- Prepara array con datos para insertar/actualizar.
- `upsert`: Inserta si no existe, actualiza si existe (basado en `period` y `rank_position`).

## 4. console.php

Configura tareas programadas (scheduling).

### Ubicación
`routes/console.php`

### Código

```php
<?php

declare(strict_types=1);

use App\Jobs\SnapshotMonthlyRankingJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SnapshotMonthlyRankingJob())
    ->monthlyOn(1, '00:05') // Primer día del mes a las 00:05
    ->withoutOverlapping(); // Evita solapamientos
```

**Explicación:**
- Ejecuta `SnapshotMonthlyRankingJob` el día 1 de cada mes a las 00:05.
- `withoutOverlapping`: Si tarda más de un mes, no ejecuta otra instancia.

## 5. OldDataSeeder

Seeder para generar datos históricos de rankings.

### Ubicación
`database/seeders/OldDataSeeder.php`

### Propósito
Crear datos falsos de posts y comentarios para meses pasados, y calcular rankings históricos.

### Código Principal

```php
final class OldDataSeeder extends Seeder
{
    private const int MONTHS_BACK = 2; // Genera datos para 2 meses atrás

    public function run(): void
    {
        $users = User::all();
        $musics = Music::all();

        if ($users->isEmpty() || $musics->isEmpty()) {
            $this->command->warn('Ejecuta primero el DatabaseSeeder.');
            return;
        }

        for ($i = self::MONTHS_BACK; $i >= 1; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $period = $monthStart->toDateString();

            $this->command->info('Generando datos para: '.$monthStart->format('Y-m'));

            foreach ($users as $user) {
                $randomMusics = $musics->random(random_int(4, 10));

                foreach ($randomMusics as $music) {
                    if (Post::query()->where('user_id', $user->id)->where('music_id', $music->id)->exists()) {
                        continue;
                    }

                    $postDate = now()->createFromTimestamp(
                        random_int($monthStart->timestamp, $monthEnd->timestamp)
                    );

                    /** @var Post $post */
                    $post = Post::factory()->create([
                        'user_id' => $user->id,
                        'music_id' => $music->id,
                        'created_at' => $postDate,
                        'updated_at' => $postDate,
                    ]);

                    // Crear comentarios aleatorios
                    $users->where('id', '!=', $user->id)
                        ->shuffle()
                        ->take(random_int(4, 10))
                        ->each(function (User $commenter) use ($post, $postDate): void {
                            Comment::factory()->create([
                                'post_id' => $post->id,
                                'user_id' => $commenter->id,
                                'created_at' => $postDate,
                                'updated_at' => $postDate,
                            ]);
                        });
                }
            }

            // Calcular y guardar rankings para ese mes
            $this->snapshotTopRated($period, $monthStart, $monthEnd);
            $this->snapshotMostRated($period, $monthStart, $monthEnd);
        }

        $this->command->info('Datos históricos generados correctamente.');
    }
}
```

**Explicación:**
- Genera datos para los últimos 2 meses.
- Para cada usuario, crea posts aleatorios para músicas aleatorias en fechas aleatorias del mes.
- Crea comentarios aleatorios en esos posts.
- Luego calcula y guarda los rankings históricos usando métodos similares al Job.

#### Métodos snapshotTopRated y snapshotMostRated
Iguales a los del Job, pero sin transacción ni limpieza de caché.

## Conclusión

Este sistema permite:
- Consultar rankings actuales (con caché para rendimiento).
- Consultar rankings históricos (almacenados en tablas dedicadas).
- Generar snapshots mensuales automáticamente.
- Poblar datos históricos para testing/desarrollo.

Los componentes trabajan juntos: el Controller sirve la API, el Resource formatea datos, el Job guarda históricos, console.php programa el Job, y el Seeder genera datos de prueba.</content>
<parameter name="filePath">DOCUMENTATION.md