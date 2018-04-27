<?php

namespace Lackone;

trait Event
{
    /**
     * 事件数组
     * @var array
     */
    private static $_event = [];

    /**
     * 添加事件回调
     * @param $name 事件名
     * @param callable $callback 回调函数
     * @param int $priority 优先级，值越大，越先执行
     * @param bool $once 是否只执行一次
     * @param bool $append 是否追加
     * @return bool
     */
    private static function _add($name, callable $callback, $priority = 0, $once = false, $append = true)
    {
        if (empty($name) || !is_callable($callback)) {
            return false;
        }
        if (!isset(self::$_event[$name])) {
            self::$_event[$name] = [];
        }
        $item = [
            'callback' => $callback,
            'priority' => $priority,
            'once' => $once,
        ];
        if (!$append) {
            self::$_event[$name] = [];
            self::$_event[$name][] = $item;
        } else {
            self::$_event[$name][] = $item;
        }
        return true;
    }

    /**
     * 事件优先级比较函数
     * @param $item1 事件项1
     * @param $item2 事件项2
     * @return int
     */
    private static function _compare($item1, $item2)
    {
        if ($item1['priority'] == $item2['priority']) {
            return 0;
        }
        return $item1['priority'] < $item2['priority'] ? 1 : -1;
    }

    /**
     * 设置事件回调
     * @param $name 事件名
     * @param callable $callback 回调函数
     * @param int $priority 优先级，值越大，越先执行
     * @param bool $append 是否追加
     * @return bool
     */
    public static function on($name, callable $callback, $priority = 0, $append = true)
    {
        return self::_add($name, $callback, $priority, false, $append);
    }

    /**
     * 设置一次性事件回调
     * @param $name 事件名
     * @param callable $callback 回调函数
     * @param int $priority 优先级，值越大，越先执行
     * @param bool $append 是否追加
     * @return bool
     */
    public static function once($name, callable $callback, $priority = 0, $append = true)
    {
        return self::_add($name, $callback, $priority, true, $append);
    }

    /**
     * 取消事件回调
     * @param $name 事件名
     * @param callable $callback 回调函数
     * @return bool
     */
    public static function off($name, callable $callback)
    {
        if (!isset(self::$_event[$name])) {
            return false;
        }
        if (is_null($callback)) {
            self::$_event[$name] = [];
            return true;
        }
        $eventList = self::$_event[$name];
        foreach ($eventList as $key => $item) {
            if ($item['callback'] == $callback) {
                unset(self::$_event[$name][$key]);
            }
        }
        return true;
    }

    /**
     * 触发事件回调
     * @param $name 事件名
     * @param array $params 参数
     * @return array
     */
    public static function trigger($name, $params = [])
    {
        $result = [];
        if (isset(self::$_event[$name])) {
            $params = func_get_args();
            array_shift($params);
            $eventList = self::$_event[$name];
            if (!empty($eventList)) {
                uasort($eventList, [get_called_class(), '_compare']);
                foreach ($eventList as $key => $item) {
                    if ($item['once']) {
                        unset(self::$_event[$name][$key]);
                    }
                    if (is_callable($item['callback'])) {
                        $result[] = call_user_func_array($item['callback'], $params);
                    }
                }
            }
        }
        return $result;
    }
}