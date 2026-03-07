-- Seed inicial: 5 silos de ejemplo
-- Datos tomados de los widgets hardcodeados (DetalleSilosWidget, MapaSilosWidget, CapacidadSilosChart)

INSERT INTO `silos` (`nombre`, `codigo`, `capacidad_kg`, `stock_actual_kg`, `cereal`, `humedad`, `estado`, `ubicacion`, `observaciones`, `created_at`, `updated_at`) VALUES
('Silo 1', 'silo_1', 100000, 60000, 'Maiz',  12.50, 'activo',        'Sector Norte', NULL, NOW(), NOW()),
('Silo 2', 'silo_2', 120000, 110000,'Soja',  11.20, 'lleno',         'Sector Norte', NULL, NOW(), NOW()),
('Silo 3', 'silo_3',  80000, 30000, 'Maiz',  13.00, 'activo',        'Sector Sur',   NULL, NOW(), NOW()),
('Silo 4', 'silo_4',  90000,     0, 'Soja',   0.00, 'en_reparacion', 'Sector Sur',   'Mantenimiento programado', NOW(), NOW()),
('Silo 5', 'silo_5', 150000, 75000, 'Trigo', 10.80, 'activo',        'Sector Este',  NULL, NOW(), NOW());
