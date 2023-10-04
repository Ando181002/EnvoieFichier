create table Utilisateur(
    idUtilisateur serial primary key,
    nomUtilisateur varchar not null,
    telephone char(10) not null unique
);
insert into Utilisateur(nomUtilisateur,telephone) values ('Ando Falimanantsoa','03245678901');
insert into Utilisateur(nomUtilisateur,telephone) values ('Miantsa Ramarojaona','0321236578');
insert into Utilisateur(nomUtilisateur,telephone) values ('Seta Rabenanahary','0326789012');
insert into Utilisateur(nomUtilisateur,telephone) values ('Nomena Falihenintsoa','0321453421');
insert into Utilisateur(nomUtilisateur,telephone) values ('Mihary Faliambinintsoa','0321568345');
insert into Utilisateur(nomUtilisateur,telephone) values ('Mianta Rajeriarinalina','0321767890');
insert into Utilisateur(nomUtilisateur,telephone) values ('Kanto Fanjava','0321267834');
insert into Utilisateur(nomUtilisateur,telephone) values ('Mikalo Fitia','0321478990');
insert into Utilisateur(nomUtilisateur,telephone) values ('Aina Rafanomezantsoa','0321267890');
insert into Utilisateur(nomUtilisateur,telephone) values ('Haingo Andrianome','03298765423');
insert into Utilisateur(nomUtilisateur,telephone) values ('Rado Rabemanantsoa','0326345893');
insert into Utilisateur(nomUtilisateur,telephone) values ('Fitia Arikanto','0321567890');

create table utilisateurOM(
    idUtilisateurOM serial primary key,
    idUtilisateur int not null references Utilisateur(idUtilisateur),
    codeSecret char(4) not null,
    solde decimal(10,2) not null default 0
);
insert into utilisateurOM(idutilisateur,codeSecret) values ('1','1234');
insert into utilisateurOM(idutilisateur,codeSecret) values ('2','2341');
insert into utilisateurOM(idutilisateur,codeSecret) values ('3','3412');
insert into utilisateurOM(idutilisateur,codeSecret) values ('4','4123');
insert into utilisateurOM(idutilisateur,codeSecret) values ('5','4321');
insert into utilisateurOM(idutilisateur,codeSecret) values ('6','3214');
insert into utilisateurOM(idutilisateur,codeSecret) values ('7','2143');
insert into utilisateurOM(idutilisateur,codeSecret) values ('8','1432');
insert into utilisateurOM(idutilisateur,codeSecret) values ('9','3421');

create or replace view v_utilisateurOm as
select uom.*,nomutilisateur,telephone from utilisateurOM uom join utilisateur u on uom.idutilisateur=u.idutilisateur;

create table Transactions(
    idtransaction serial primary key,
    datetransaction timestamp not null,
    idEnvoyeur int not null references utilisateurOM(idUtilisateurOM),
    idRecepteur int not null references utilisateurOM(idUtilisateurOM),
    objet text,
    montant decimal(10,2) not null,
    statut int not null
);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),1,2,'Versement','10000',1);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),3,4,'Paiement facture','100000',1);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),5,6,'Loyer','50000',1);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),1,2,'Versement2','1000',1);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),3,1,'Test','5000',1);
insert into Transactions(datetransaction,idEnvoyeur,idRecepteur,objet,montant,statut) values (now(),4,1,'Test2','15000',1);

create or replace view soldeCompte as
SELECT 
    SUM(CASE
        WHEN idenvoyeur = '1' THEN -montant  
        WHEN idrecepteur = '1' THEN montant  
        ELSE 0 
    END) AS solde
FROM Transactions
WHERE idenvoyeur = '1' OR idrecepteur = '1' and statut=1;

