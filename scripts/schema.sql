CREATE DATABASE IF NOT EXISTS datatime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE datatime;

CREATE TABLE Projet(
   id_projet INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_projet)
);

CREATE TABLE Salarie(
   id_salarie INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   poste VARCHAR(50) NOT NULL,
   contrat VARCHAR(50) NOT NULL,
   taux_journalier_moyen DECIMAL(15,2) NOT NULL,
   role VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_salarie)
);

CREATE TABLE Utilisateur(
   id_utilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL UNIQUE,
   mot_de_passe VARCHAR(255) NOT NULL,
   PRIMARY KEY(id_utilisateur)
);

CREATE TABLE Client(
   id_client INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_client)
);

CREATE TABLE Absence(
   id_absence INT AUTO_INCREMENT,
   type ENUM('conge','maladie') NOT NULL,
   debut DATE NOT NULL,
   fin DATE,
   motif VARCHAR(50),
   id_salarie INT NOT NULL,
   PRIMARY KEY(id_absence),
   FOREIGN KEY(id_salarie) REFERENCES Salarie(id_salarie)
);

CREATE TABLE Tache(
   id_tache INT AUTO_INCREMENT,
   Nom VARCHAR(50) NOT NULL,
   temps_previsionnel DECIMAL(15,1),
   temps_passe DECIMAL(15,1),
   debut DATE,
   fin DATE,
   statut ENUM('a_faire','en_cours','termine') NOT NULL,
   id_projet INT NOT NULL,
   id_salarie INT NOT NULL,
   PRIMARY KEY(id_tache),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet),
   FOREIGN KEY(id_salarie) REFERENCES Salarie(id_salarie)
);

CREATE TABLE affecter(
   id_projet INT,
   id_salarie INT,
   debut DATE NOT NULL,
   fin DATE,
   PRIMARY KEY(id_projet, id_salarie),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet),
   FOREIGN KEY(id_salarie) REFERENCES Salarie(id_salarie)
);

CREATE TABLE Soumettre(
   id_projet INT,
   id_client INT,
   PRIMARY KEY(id_projet, id_client),
   FOREIGN KEY(id_projet) REFERENCES Projet(id_projet),
   FOREIGN KEY(id_client) REFERENCES Client(id_client)
);
