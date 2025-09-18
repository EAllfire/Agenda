CREATE DATABASE agenda_hospital;
USE agenda_hospital;

-- Tabla usuarios
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  tipo ENUM('admin','caja','lectura') NOT NULL
);


-- Tabla pacientes
CREATE TABLE pacientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100),
  telefono VARCHAR(20),
  correo VARCHAR(100),
  diagnostico VARCHAR(255),
  tipo VARCHAR(50),
  origen VARCHAR(100)
);

-- Tabla profesionales
CREATE TABLE profesionales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  especialidad VARCHAR(100)
);

-- Tabla servicios
CREATE TABLE servicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
);

INSERT INTO servicios (nombre) VALUES
('Radiografía'),
('Resonancia Magnética'),
('Tomografía'),
('Mastografía'),
('Sonografía');

-- Tabla citas
CREATE TABLE citas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  paciente_id INT,
  profesional_id INT,
  servicio_id INT, -- Radiografía, RM, etc.
  modalidad VARCHAR(100), -- opcional, ej: "simple", "contrastada"
  estado ENUM('reservado','confirmado','asistió','no asistió','pendiente','en espera') DEFAULT 'pendiente',
  color ENUM('azul','naranja','rosa','coral','rojo','verde') DEFAULT 'rojo',
  tipo VARCHAR(50),
  FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
  FOREIGN KEY (profesional_id) REFERENCES profesionales(id),
  FOREIGN KEY (servicio_id) REFERENCES servicios(id)
);
