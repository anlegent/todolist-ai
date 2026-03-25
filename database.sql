-- database.sql - Script de création de la base de données

CREATE DATABASE IF NOT EXISTS todolist_db;
USE todolist_db;

CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion de quelques données de test
INSERT INTO todos (title, description, completed) VALUES
('Apprendre PHP', 'Maîtriser les concepts de base du PHP', 0),
('Créer une API REST', 'Développer une API RESTful complète', 1),
('Tester avec Postman', "Valider tous les endpoints de l'API", 0);
