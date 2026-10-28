USE datatime;

INSERT INTO Projet (id_projet, nom) VALUES
(1, 'Refonte site vitrine'),
(2, 'Application mobile logistique'),
(3, 'Migration infrastructure cloud');

INSERT INTO Salarie (id_salarie, nom, prenom, poste, contrat, taux_journalier_moyen, role) VALUES
(1, 'Dupont', 'Marie', 'Développeuse Full-Stack', 'CDI', 450.00, 'employe'),
(2, 'Martin', 'Lucas', 'Chef de projet', 'CDI', 600.00, 'manager'),
(3, 'Bernard', 'Sophie', 'Designer UX/UI', 'Freelance', 500.00, 'employe'),
(4, 'Petit', 'Hugo', 'DevOps', 'CDI', 550.00, 'employe');

INSERT INTO Client (id_client, nom) VALUES
(1, 'Acme Corp'),
(2, 'Globex SARL'),
(3, 'Initech');

INSERT INTO Absence (id_absence, type, debut, fin, motif, id_salarie) VALUES
(1, 'conge', '2026-08-01', '2026-08-15', 'Congés été', 1),
(2, 'maladie', '2026-10-10', '2026-10-12', 'Grippe', 3);

INSERT INTO Tache (id_tache, Nom, temps_previsionnel, temps_passe, debut, fin, statut, id_projet, id_salarie) VALUES
(1, 'Maquettes page accueil', 5.0, 4.5, '2026-09-01', '2026-09-10', 'termine', 1, 3),
(2, 'Intégration API paiement', 8.0, 3.0, '2026-09-11', '2026-09-25', 'en_cours', 1, 1),
(3, 'Mise en place pipeline CI/CD', 6.0, 0.0, '2026-10-20', '2026-11-05', 'a_faire', 3, 4),
(4, 'Spécifications fonctionnelles app mobile', 4.0, 4.0, '2026-09-01', '2026-09-05', 'termine', 2, 2);

INSERT INTO affecter (id_projet, id_salarie, debut, fin) VALUES
(1, 1, '2026-09-01', NULL),
(1, 3, '2026-09-01', NULL),
(2, 2, '2026-08-15', NULL),
(3, 4, '2026-10-20', NULL);

INSERT INTO Soumettre (id_projet, id_client) VALUES
(1, 1),
(2, 2),
(3, 3);


INSERT INTO Utilisateur (nom, email, mot_de_passe) VALUES
('Admin', 'admin@datatime.local', '$2y$10$6i9csjc.9Ku1AxSdgipI9.J5OMPN.KJG/o/pS3lFJitZutkg.DxL2');
