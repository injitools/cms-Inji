<?php

class Model
{

    public $modelLogKey = 0;
    public $params = [];
    public $loadedRelations = [];
    static $labels = [];
    static $forms = [];
    private $cols = [];

    function __construct($params = array())
    {
        $this->setParams($params);
    }

    function cols()
    {
        if (!$this->cols) {
            $class = get_called_class();
            $this->cols = Inji::app()->db->getTableCols($class::table());
        }
        return $this->cols;
    }

    static function table()
    {
        return null;
    }

    static function index()
    {
        return null;
    }

    static function colPrefix()
    {
        return null;
    }

    static function relations()
    {
        return null;
    }

    static function nameCol()
    {
        return null;
    }

    static function get($param = null, $col = null)
    {
        $class = get_called_class();

        if (is_array($param)) {
            Inji::app()->db->where($param);
        } else {
            if ($col === null) {

                $col = $class::index();
            }
            if ($param !== null) {
                Inji::app()->db->where($col, $param);
            } else {
                return false;
            }
        }
        if (!Inji::app()->db->where) {
            return false;
        }
        $result = Inji::app()->db->select($class::table());
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        if (!$row) {
            return false;
        }
        return new $class($row);
    }

    static function get_list($options = array())
    {
        $class = get_called_class();
        $return = array();
        if (!empty($options['where']))
            Inji::app()->db->where($options['where']);
        if (!empty($options['order']))
            Inji::app()->db->order($options['order']);
        if (!empty($options['join']))
            Inji::app()->db->join($options['join']);
        if (!empty($options['limit']))
            $limit = (int) $options['limit'];
        else {
            $limit = 0;
        }
        if (!empty($options['start']))
            $start = (int) $options['start'];
        else {
            $start = 0;
        }
        if ($limit || $start) {
            Inji::app()->db->limit($start, $limit);
        }
        if (isset($options['key'])) {
            $key = $options['key'];
        } else {
            $key = $class::index();
        }
        $result = Inji::app()->db->select($class::table());
        $rows = Inji::app()->db->result_array($result, $key);
        if (empty($options['array'])) {
            foreach ($rows as $row) {
                if ($key) {
                    $return[$row[$key]] = new $class($row);
                } else {
                    $return[] = new $class($row);
                }
            }
            return $return;
        }
        return $rows;
    }

    static function getCount($options = array())
    {
        $class = get_called_class();
        $return = array();
        if (!empty($options['where']))
            Inji::app()->db->where($options['where']);
        if (!empty($options['join']))
            Inji::app()->db->join($options['join']);

        Inji::app()->db->cols = 'COUNT(*) as `count`';
        $result = Inji::app()->db->select($class::table());
        $count = $result->fetch_assoc();
        return $count['count'];
    }

    static function update($params, $where = [])
    {
        $class = get_called_class();
        $cols = Inji::app()->db->getTableCols($class::table());

        $values = array();
        foreach ($cols as $col => $param) {
            if (isset($params[$col]))
                $values[$col] = $params[$col];
        }
        if (!$values)
            return false;
        if ($where) {
            Inji::app()->db->where($where);
        }
        Inji::app()->db->update($class::table(), $values);
    }
    
    function pk(){
        return $this->{$this->index()};
    }

    function beforeSave()
    {
        
    }

    function save()
    {
        $this->beforeSave();

        $values = array();


        foreach ($this->cols() as $col => $param) {
            if (isset($this->params[$col]))
                $values[$col] = $this->params[$col];
        }
        if (!$values)
            return false;

        if (!empty($this->params[$this->index()])) {
            if ($this->get($this->params[$this->index()])) {
                Inji::app()->db->where($this->index(), $this->params[$this->index()]);
                Inji::app()->db->update($this->table(), $values);
            } else {
                $this->params[$this->index()] = Inji::app()->db->insert($this->table(), $values);
            }
        } else {
            $this->params[$this->index()] = Inji::app()->db->insert($this->table(), $values);
        }
        Inji::app()->db->where($this->index(), $this->params[$this->index()]);
        $result = Inji::app()->db->select($this->table());
        $this->params = $result->fetch_assoc();
        $this->afterSave();
        return $this->{$this->index()};
    }

    function afterSave()
    {
        
    }

    function beforeDelete()
    {
        
    }

    function delete()
    {
        $this->beforeDelete();
        if (!empty($this->params[$this->index()])) {
            Inji::app()->db->where($this->index(), $this->params[$this->index()]);
            $result = Inji::app()->db->delete($this->table());
            if ($result) {
                $this->afterDelete();
                return $result;
            }
        }
        return false;
    }

    function afterDelete()
    {
        
    }

    static function findRelation($col)
    {
        $class = get_called_class();
        foreach ($class::relations() as $relName => $rel) {
            if ($rel['col'] == $col)
                return $relName;
        }
        return NULL;
    }

    function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }

    function loadRelation($name, $params = [])
    {

        $relations = $this->relations();
        if (isset($relations[$name])) {
            $relation = $relations[$name];
            if (!isset($relation['type']))
                $type = 'one';
            else
                $type = $relation['type'];

            switch ($type) {
                case 'relTable':
                    if (!$this->{$this->index()}) {
                        return [];
                    }
                    Inji::app()->db->where($relation['relTablePrefix'] . $this->index(), $this->{$this->index()});
                    $ids = Inji::app()->db->result_array(Inji::app()->db->select($relation['relTable']), $relation['relTablePrefix'] . $relation['model']::index());
                    $getType = 'get_list';
                    $options = [
                        'where' => [$relation['model']::index(), implode(',', array_keys($ids)), 'IN'],
                        'array' => (!empty($params['array'])) ? true : false,
                        'key' => (isset($params['key'])) ? $params['key'] : ((isset($relation['resultKey'])) ? $relation['resultKey'] : null),
                        'start' => (isset($params['start'])) ? $params['start'] : ((isset($relation['start'])) ? $relation['start'] : null),
                        'order' => (isset($params['order'])) ? $params['order'] : ((isset($relation['order'])) ? $relation['order'] : null),
                        'limit' => (isset($params['limit'])) ? $params['limit'] : ((isset($relation['limit'])) ? $relation['limit'] : null),
                    ];
                    break;
                case 'many':
                    if (!$this->{$this->index()}) {
                        return [];
                    }
                    $getType = 'get_list';
                    $options = [
                        'where' => [$relation['col'], $this->{$this->index()}],
                        'join' => (isset($relation['params']['join'])) ? $relation['params']['join'] : null,
                        'order' => (isset($relation['params']['order'])) ? $relation['params']['join'] : null,
                        'key' => (isset($params['key'])) ? $params['key'] : ((isset($relation['resultKey'])) ? $relation['resultKey'] : null),
                        'array' => (!empty($params['array'])) ? true : false,
                        'order' => (isset($params['order'])) ? $params['order'] : ((isset($relation['order'])) ? $relation['order'] : null),
                        'start' => (isset($params['start'])) ? $params['start'] : ((isset($relation['start'])) ? $relation['start'] : null),
                        'limit' => (isset($params['limit'])) ? $params['limit'] : ((isset($relation['limit'])) ? $relation['limit'] : null),
                    ];
                    break;
                default:
                    if ($this->$relation['col'] === NULL)
                        return NULL;
                    $getType = 'get';
                    $options = $this->$relation['col'];
            }

            if (!empty($params['count'])) {
                return $relation['model']::getCount($options);
            } else {
                $this->loadedRelations[$name][json_encode($params)] = $relation['model']::$getType($options);
            }
            return $this->loadedRelations[$name][json_encode($params)];
        }
        return NULL;
    }

    function addRelation($relName, $objectId)
    {
        $relations = $this->relations();
        if (isset($relations[$relName])) {
            $relation = $relations[$relName];
            Inji::app()->db->where($relation['relTablePrefix'] . $this->index(), $this->{$this->index()});
            Inji::app()->db->where($relation['relTablePrefix'] . $relation['model']::index(), $objectId);
            $isset = Inji::app()->db->select($relation['relTable'])->fetch_assoc();
            if ($isset)
                return true;

            Inji::app()->db->insert($relation['relTable'], [
                $relation['relTablePrefix'] . $this->index() => $this->{$this->index()},
                $relation['relTablePrefix'] . $relation['model']::index() => $objectId
            ]);
            return true;
        }
        return false;
    }

    function checkFormAccess($formName)
    {
        if ($formName == 'manage' && !Inji::app()->users->cur->isAdmin()) {
            return false;
        }
        return true;
    }

    function __call($name, $params)
    {
        return call_user_func_array([$this, 'loadRelation'], array_merge([$name], $params));
    }

    function __get($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        if (isset($this->loadedRelations[$name][json_encode([])])) {
            return $this->loadedRelations[$name][json_encode([])];
        }
        return $this->loadRelation($name);
    }

    function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

}
