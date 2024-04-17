<?php

declare(strict_types=1);

namespace ReverseRegex\Generator;

use ArrayAccess;
use ArrayObject;
use Countable;
use Iterator;
use SplObjectStorage;
use Closure;

/**
 *  Base to all Generator Scopes.
 *
 *  @author Lewis Dyer <getintouch@icomefromthenet.com>
 *
 *  @since 0.0.1
 */
class Node implements ArrayAccess, Countable, Iterator
{
    /**
     *  @var string name of the node
     */
    protected $label;

    /**
     *  @var ArrayObject container for node metadata
     */
    protected $attrs;

    /**
     *  @var SplObjectStorage container for node relationships
     */
    protected $links;

    /**
     *  Class Constructor.
     *
     *  @param string $label
     */
    public function __construct($label = 'node')
    {
        $this->attrs = new ArrayObject();
        $this->links = new SplObjectStorage();

        $this->setLabel($label);
    }

    /**
     *  Fetch the nodes label.
     *
     *  @return string the nodes label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *  Sets the node label.
     *
     *  @param string $label the nodes label
     */
    public function setLabel($label)
    {
        if (!(is_scalar($label) || null === $label)) {
            return false;
        }

        $this->label = $label;
    }

    /**
     *  Attach a node.
     *
     *  @param Node $node the node to attach
     *
     *  @return Node
     */
    public function &attach(Node $node)
    {
        $this->links->attach($node);

        return $this;
    }

    /**
     *  Detach a node.
     *
     *  @param Node $node the node to remove
     *
     *  @return Node
     */
    public function &detach(Node $node)
    {
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                $this->links->detach($node);
            }
        }

        return $this;
    }

    /**
     *  Search for node in its relations.
     *
     *  @param Node $node the node to search for
     *
     *  @return bool true if found
     */
    public function contains(Node $node)
    {
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Apply a closure to all relations.
     *
     *  @param Closer the function to apply
     */
    public function map(Closure $function)
    {
        foreach ($this->links as $node) {
            $function($node);
        }
    }

    //------------------------------------------------------------------
    // Countable

    public function count(): int
    {
        return count($this->links);
    }

    //------------------------------------------------------------------
    // Iterator

    public function current(): mixed
    {
        return $this->links->current();
    }

    public function key(): mixed
    {
        return $this->links->key();
    }

    public function next(): void
    {
        $this->links->next();
    }

    public function rewind(): void
    {
        $this->links->rewind();
    }

    public function valid(): bool
    {
        return $this->links->valid();
    }

    //------------------------------------------------------------------
    // ArrayAccess Implementation
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->attrs->offsetGet($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->attrs->offsetSet($key, $value);
    }

    public function offsetExists($key): bool
    {
        return $this->attrs->offsetExists($key);
    }

    public function offsetUnset($key): void
    {
        $this->attrs->offsetUnset($key);
    }
}

/* End of Class */
