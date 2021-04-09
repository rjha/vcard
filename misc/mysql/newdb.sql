

CREATE USER 'yuktix'@'localhost' IDENTIFIED BY 'Yuktix#mysql';
create database vcard  character set utf8mb4 collate utf8mb4_unicode_ci;
grant all privileges on vcard.* to 'yuktix'@'localhost' with grant option;

