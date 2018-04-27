# event
一个简单的php事件类，用于绑定和解绑事件，触发回调。

## 如何使用

### 1、引入
<code>
    composer require lackone/event
</code>

### 2、使用trait
<code>
    use Lackone\Event;
</code>

### 3、例子
<code>
    <?php
    require 'vendor/autoload.php';
    
    class Test
    {
        use Lackone\Event;
    
        public static function call0()
        {
            //获取参数
            var_dump(func_get_args());
        }
    
        public static function call1()
        {
            echo 'call1...<br>';
        }
    
        public static function call2()
        {
            echo 'call2...<br>';
        }
    
        public static function call3()
        {
            echo 'call3...<br>';
        }
    }
    
    //绑定事件
    Test::on('test', ['Test', 'call0']);
    //触发事件
    Test::trigger('test', ['hello', 'world']);
    
    echo '<hr>';
    
    //设置事件优先级，优先级越大，越先执行
    Test::on('pro', ['Test', 'call1'], 1);
    Test::on('pro', ['Test', 'call2'], 2);
    Test::on('pro', ['Test', 'call3'], 3);
    Test::trigger('pro');
    
    echo '<hr>';
    
    //设置一次性事件，只能触发一次
    Test::once('one', ['Test', 'call1']);
    Test::trigger('one');
    Test::trigger('one');
    
    echo '<hr>';
    
    //解绑事件
    Test::on('off', ['Test', 'call1']);
    Test::on('off', ['Test', 'call2']);
    Test::on('off', ['Test', 'call3']);
    //解绑具体的某个回调
    Test::off('off', ['Test', 'call2']);
    Test::trigger('off');
</code>
