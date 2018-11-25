<?php

namespace App\base;

use App\Log;
use PDO;
use PDOStatement;

/**
 * Подготовленный запрос, который пишет в лог строку запроса и
 * привязанные параметры перед выполнением запроса
 * @author Roman Mitasov <metas_roman@mail.ru>
 * @package App\base
 *
 * @uses Log
 * @see LoggedPDO
 */
class LoggedPdoStatement extends PDOStatement
{
    private $_boundValues;

    /**
     * @inheritdoc
     */
    public function execute($input_parameters = null)
    {
        if (APP_DEBUG) {
            if ($input_parameters !== null) {
                $this->_boundValues = $input_parameters;
            }

            Log::sql($this->queryString, $this->_boundValues);
        }

        return parent::execute($input_parameters);
    }

    /**
     * @inheritdoc
     */
    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        if (APP_DEBUG) {
            $this->_boundValues[$parameter] = $value;
        }

        parent::bindValue($parameter, $value, $data_type);
    }

    /**
     * @inheritdoc
     */
    public function bindParam(
        $parameter,
        &$variable,
        $data_type = PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    ) {
        if (APP_DEBUG) {
            $this->_boundValues[$parameter] = &$variable;
        }

        parent::bindParam($parameter, $variable, $data_type, $length, $driver_options);
    }
}
