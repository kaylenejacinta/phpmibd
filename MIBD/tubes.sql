CREATE DATABASE FRS_db;
GO

USE FRS_db;
GO

DROP TABLE IF EXISTS Jadwal_Matkul;
DROP TABLE IF EXISTS Enroll;
DROP TABLE IF EXISTS FRS;
DROP TABLE IF EXISTS Dosen;
DROP TABLE IF EXISTS Mata_Kuliah;
DROP TABLE IF EXISTS Semester;
DROP TABLE IF EXISTS Mahasiswa;
GO

/* =========================
   MASTER TABLE
   ========================= */

CREATE TABLE Mahasiswa (
    NPM INT IDENTITY(200,1) PRIMARY KEY,
    Nama VARCHAR(50) NOT NULL,
    Prodi VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(50) NOT NULL
);

CREATE TABLE Semester (
    Id_Semester INT IDENTITY(1,1) PRIMARY KEY,
    Tahun_Akademik SMALLINT NOT NULL,
    Periode SMALLINT NOT NULL
);

CREATE TABLE Dosen (
    Id_Dosen INT IDENTITY(100,1) PRIMARY KEY,
    Nama VARCHAR(50) NOT NULL,
    Prodi VARCHAR(50) NOT NULL,
    Email VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(50) NOT NULL
);

CREATE TABLE Mata_Kuliah (
    Kode_Matkul INT IDENTITY(300,1) PRIMARY KEY,
    Nama_Matkul VARCHAR(100) NOT NULL,
    SKS SMALLINT NOT NULL,
    Id_Semester INT NOT NULL,

    FOREIGN KEY (Id_Semester)
        REFERENCES Semester(Id_Semester)
);

CREATE TABLE Jadwal_Matkul (
    Id_Jadwal VARCHAR(20) PRIMARY KEY,
    Hari VARCHAR(20),
    Jam_Mulai TIME,
    Jam_Selesai TIME,
    Ruangan VARCHAR(20),

    Kode_Matkul INT NOT NULL,
    Id_Semester INT NOT NULL,
    Id_Dosen INT NOT NULL,

    FOREIGN KEY (Kode_Matkul)
        REFERENCES Mata_Kuliah(Kode_Matkul),

    FOREIGN KEY (Id_Semester)
        REFERENCES Semester(Id_Semester),

    FOREIGN KEY (Id_Dosen)
        REFERENCES Dosen(Id_Dosen)
);

/* =========================
   TRANSACTION TABLE
   ========================= */

CREATE TABLE FRS (
    Id_FRS INT IDENTITY(1,1) PRIMARY KEY,

    NPM INT NOT NULL,
    Id_Semester INT NOT NULL,

    Status VARCHAR(20) DEFAULT 'Draft',

    FOREIGN KEY (NPM)
        REFERENCES Mahasiswa(NPM),

    FOREIGN KEY (Id_Semester)
        REFERENCES Semester(Id_Semester)
);

CREATE TABLE Enroll (
    Id_Enroll INT IDENTITY(1,1) PRIMARY KEY,

    Tanggal_Ambil DATE NOT NULL,
    Id_FRS INT NOT NULL,
    Kode_Matkul INT NOT NULL,

    FOREIGN KEY (Id_FRS)
        REFERENCES FRS(Id_FRS),

    FOREIGN KEY (Kode_Matkul)
        REFERENCES Mata_Kuliah(Kode_Matkul)
);

GO

/* =========================
   DATA MASTER
   ========================= */

INSERT INTO Mahasiswa
(Nama, Prodi, Email, Password)
VALUES
('Nursela Febriani','Informatika','nursela@student.id','123'),
('Budi Santoso','Informatika','budi@student.id','123'),
('Citra Lestari','Informatika','citra@student.id','123'),
('Dimas Pratama','Informatika','dimas@student.id','123'),
('Eka Putri','Informatika','eka@student.id','123');

INSERT INTO Semester
(Tahun_Akademik, Periode)
VALUES
(2024,1),
(2024,2),
(2025,1),
(2025,2),
(2026,1);

INSERT INTO Dosen
(Nama, Prodi, Email, Password)
VALUES
('Artem Wing','Economics','artem@dosen.id','123'),
('Rina Wijaya','Informatika','rina@dosen.id','123'),
('Andi Saputra','Informatika','andi@dosen.id','123'),
('Sari Dewi','Informatika','sari@dosen.id','123'),
('Tono Hartono','Informatika','tono@dosen.id','123');

INSERT INTO Mata_Kuliah
(Nama_Matkul, SKS, Id_Semester)
VALUES
('Algoritma dan Pemrograman',3,1),
('Basis Data',3,1),
('Pemrograman Web',3,2),
('Struktur Data',4,2),
('Kecerdasan Buatan',3,3);

INSERT INTO Jadwal_Matkul
VALUES
('JDW001','Senin','08:00','10:00','R101',300,1,100),
('JDW002','Selasa','08:00','10:00','R102',301,1,101),
('JDW003','Rabu','10:00','12:00','R103',302,2,102),
('JDW004','Kamis','13:00','15:00','R104',303,2,103),
('JDW005','Jumat','15:00','17:00','R105',304,3,104);

GO

/* =========================
   DATA TRANSAKSI
   ========================= */

INSERT INTO FRS
(NPM, Id_Semester, Status)
VALUES
(200,1,'Submitted'),
(201,2,'Submitted'),
(202,3,'Draft'),
(203,4,'Draft'),
(204,5,'Submitted');

GO

INSERT INTO Enroll
(Tanggal_Ambil, Id_FRS, Kode_Matkul)
VALUES
('2025-01-10',1,300),
('2025-01-10',1,301),
('2025-01-10',1,302),
('2025-01-10',1,303),
('2025-01-10',1,304),

('2025-01-11',2,300),
('2025-01-11',2,301),
('2025-01-11',2,302),
('2025-01-11',2,303),
('2025-01-11',2,304),

('2025-01-12',3,300),
('2025-01-12',3,301),
('2025-01-12',3,302),
('2025-01-12',3,303),
('2025-01-12',3,304),

('2025-01-13',4,300),
('2025-01-13',4,301),
('2025-01-13',4,302),
('2025-01-13',4,303),
('2025-01-13',4,304),

('2025-01-14',5,300),
('2025-01-14',5,301),
('2025-01-14',5,302),
('2025-01-14',5,303),
('2025-01-14',5,304),

('2025-01-15',1,300),
('2025-01-15',2,301),
('2025-01-15',3,302),
('2025-01-15',4,303),
('2025-01-15',5,304);

GO