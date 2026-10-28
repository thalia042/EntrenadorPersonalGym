<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rutina - {{ $routine->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 20px; }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #d97706; font-size: 24px; }
        .info { margin-bottom: 20px; font-size: 14px; }
        .info p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 13px; }
        th { background-color: #f3f4f6; color: #1f2937; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Personal Trainer Gym</h1>
        <p>Plan de Entrenamiento Personalizado</p>
    </div>

    <div class="info">
        <p><strong>Cliente:</strong> {{ $routine->user->name ?? 'Sin asignar' }}</p>
        <p><strong>Rutina:</strong> {{ $routine->name }}</p>
        <p><strong>Descripción:</strong> {{ $routine->description ?? 'Sin observaciones' }}</p>
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y') }}</p>
    </div>

    <h3>Ejercicios Asignados</h3>
    <table>
        <thead>
            <tr>
                <th>Ejercicio</th>
                <th>Grupo Muscular</th>
                <th>Series / Repeticiones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($routine->exercises as $exercise)
                <tr>
                    <td>{{ $exercise->name }}</td>
                    <td>{{ $exercise->muscle_group }}</td>
                    <td>{{ $exercise->pivot->sets_reps ?? 'Según indicación' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No hay ejercicios registrados en esta rutina.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
