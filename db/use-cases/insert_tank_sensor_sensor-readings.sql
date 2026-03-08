START TRANSACTION;

INSERT INTO users (username)
VALUES ('jude');

INSERT INTO user_activity_logs (user_id, action)
VALUES (LAST_INSERT_ID(), 'put status of the tank');

INSERT INTO tank (tankname, location_add, capacity, status_tank)
VALUES ('Tank 1', 'Jude Crib', '3,542L', 'Active');

INSERT INTO sensors (tank_id, sensor_type, model, unit, is_active)
VALUES (LAST_INSERT_ID(), 'Water Level', 'Model 1', '1', 'Active');

INSERT INTO sensor_readings (sensor_id, user_id, anomaly)
VALUES (LAST_INSERT_ID(), 1, 'None');

COMMIT;