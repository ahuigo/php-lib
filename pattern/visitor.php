<?php
/**
 * 抽象基类
 */
abstract class Unit
{
    /**
     * 获取名称
     */
    abstract public function getName();

    /**
     * 用来接受访问者对象，回调访问者的visit方法
     * 非常关键的方法
     */
    public function accept(Visitor $visitor)
    {
        $method = 'visit' . get_class($this);
        if (method_exists($visitor, $method)) {
            $visitor->$method($this);
        }
    }
}

/**
 * Cpu类
 */
class Cpu extends Unit
{
    public function getName()
    {
        return 'i am cpu';
    }
}

/**
 * Memory类
 */
class Memory extends Unit
{
    public function getName()
    {
        return 'i am memory';
    }
}

/**
 * Keyboard类
 */
class Keyboard extends Unit
{
    public function getName()
    {
        return 'i am keyboard';
    }
}

/**
 * Keyboard类
 */
interface Visitor
{
    public function visitCpu(Cpu $cpu);
    public function visitMemory(Memory $memory);
    public function visitKeyboard(Keyboard $keyboard);
}

/**
 *
 */
class PrintVisitor implements Visitor
{
    public function visitCpu(Cpu $cpu)
    {
        echo "hello, " . $cpu->getName() . "\n";
    }

    public function visitMemory(Memory $memory)
    {
        echo "hello, " . $memory->getName() . "\n";
    }

    public function visitKeyboard(Keyboard $keyboard)
    {
        echo "hello, " . $keyboard->getName() . "\n";
    }
}

/**
 *
 */
class Computer
{
    protected $_items = [];

    public function add(Unit $unit)
    {
        $this->_items[] = $unit;
    }

    /**
     * 调用各个组件的accept方法
     */
    public function accept(Visitor $visitor)
    {
        foreach ($this->_items as $item) {
            $item->accept($visitor);
        }
    }
}

$computer = new Computer();
$computer->add(new Cpu());
$computer->add(new Memory());
$computer->add(new Keyboard());

$printVisitor = new PrintVisitor();
$computer->accept($printVisitor);

/** output
 *
hello, i am cpu
hello, i am memory
hello, i am keyboard
 */
