
drop table if exists card_master;
create table card_master (
    id BIGINT unsigned NOT NULL auto_increment,
    name varchar(64) not null,
    email varchar(64) not null,
    version int default 0,
    created_on timestamp default current_timestamp,
    updated_on timestamp default current_timestamp ,
    PRIMARY KEY (id)) ENGINE = InnoDB default character SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

alter table card_master add constraint uniq_email unique(email);



drop table if exists card_trash;
create table card_trash (
    id BIGINT unsigned NOT NULL auto_increment,
    email varchar(64) not null,
    version int default 0,
    created_on timestamp default current_timestamp,
    updated_on timestamp default current_timestamp ,
    PRIMARY KEY (id)) ENGINE = InnoDB default character SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

alter table card_trash add constraint uniq_email unique(email);


alter table card_master add column country_code int;
alter table card_master add column phone varchar(16);
alter table card_master add column source varchar(16);
