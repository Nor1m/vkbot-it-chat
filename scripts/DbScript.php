<?php

namespace Scripts;

use App\base\Db;
use App\Log;
use PDO;

class DbScript
{
    /**
     * @throws \Exception
     */
    public static function initClass()
    {
        // для загрузки конфигов
        require_once __DIR__ . '/../vendor/autoload.php';
        var_dump($_SERVER['DOCUMENT_ROOT']); die();
        echo ROOT_PATH; die();

        Log::init(__DIR__ . '/../storage/logs/log.txt');
        Db::init(DB, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true
        ));
    }

    /**
     * @throws \Exception
     */
    public static function init()
    {
        self::initClass();

        Db::execute(<<<SQL
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS tech CASCADE;
DROP TABLE IF EXISTS tech_proposed CASCADE;
DROP TABLE IF EXISTS `user` CASCADE;
DROP TABLE IF EXISTS user_proposed CASCADE;
DROP TABLE IF EXISTS user_tech CASCADE;

CREATE TABLE tech (
  id          BIGINT UNSIGNED AUTO_INCREMENT,
  `code`      VARCHAR(255) NOT NULL COMMENT 'Уникальный строковый идентификатор',
  `name`      TINYTEXT NOT NULL COMMENT 'Название технологии',
  description TEXT NULL COMMENT 'Описание технологии',
  CONSTRAINT id
  UNIQUE (id),
  CONSTRAINT stack_item_code_uindex
  UNIQUE (code)
) COMMENT 'Технология (для стека юзеров)';

ALTER TABLE tech ADD PRIMARY KEY (id);

CREATE TABLE tech_proposed (
  id     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(255) NOT NULL COMMENT 'Код технологии, предложенной для добавления',
  closed TINYINT(1) DEFAULT '0' NOT NULL COMMENT 'Предложение закрыто',
  CONSTRAINT id UNIQUE (id),
  CONSTRAINT tech_proposed_code_ui UNIQUE (code)
) COMMENT 'Предложенные юзерами технологии';

ALTER TABLE tech_proposed ADD PRIMARY KEY (id);

CREATE TABLE `user` (
  vk_id      BIGINT UNSIGNED NOT NULL COMMENT 'Id vk-пользователя',
  first_name VARCHAR(255) NULL COMMENT 'Имя',
  last_name  VARCHAR(255) NULL COMMENT 'Фамилия',
  patronymic VARCHAR(255) NULL COMMENT 'Отчество',
  CONSTRAINT vk_id
  UNIQUE (vk_id)
) COMMENT 'Пользователь';

ALTER TABLE `user` ADD PRIMARY KEY (vk_id);

CREATE TABLE user_proposed (
  user_id     BIGINT UNSIGNED NOT NULL,
  proposed_id BIGINT UNSIGNED NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  PRIMARY KEY (user_id, proposed_id),
  CONSTRAINT user_proposed_fk_proposed
    FOREIGN KEY (proposed_id) REFERENCES tech_proposed (id)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
  CONSTRAINT user_proposed_fk_user
    FOREIGN KEY (user_id) REFERENCES `user` (vk_id)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) COMMENT 'Предложенные юзером технологии';

CREATE TABLE user_tech (
  user_id BIGINT UNSIGNED NOT NULL COMMENT 'Ссылка на пользователя',
  tech_id BIGINT UNSIGNED NOT NULL COMMENT 'Ссылка на технологию',
  ord      INT NOT NULL COMMENT 'Порядковый номер, для вывода и удаления',
  PRIMARY KEY (tech_id, user_id),
  CONSTRAINT user_tech_ui
    UNIQUE (user_id, tech_id),
  CONSTRAINT user_tech_tech_id_fk
    FOREIGN KEY (tech_id) REFERENCES tech (id)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
  CONSTRAINT user_tech_user_vk_id_fk
    FOREIGN KEY (user_id) REFERENCES `user` (vk_id)
      ON UPDATE CASCADE
      ON DELETE CASCADE
) COMMENT 'Связующая между пользователем и стеком';

SET FOREIGN_KEY_CHECKS = 1;
SQL
        );
    }

    /**
     * @throws \Exception
     */
    public static function drop()
    {
        self::initClass();

        Db::execute(<<<SQL
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tech2 CASCADE;
DROP TABLE IF EXISTS tech2_proposed2 CASCADE;
DROP TABLE IF EXISTS `user2` CASCADE;
DROP TABLE IF EXISTS user2_proposed2 CASCADE;
DROP TABLE IF EXISTS user2_tech2 CASCADE;
SET FOREIGN_KEY_CHECKS = 1;
SQL
        );
    }
}