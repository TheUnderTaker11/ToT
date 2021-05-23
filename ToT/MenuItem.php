<?php

/**
 * Model menu_items objects and provide storage/retrieval from the database.
 *
 * To change values in database, simply change the variables then call the save() function.
 * @author Clay Bellou
 */

namespace ToT;

class MenuItem
{

    /**
     * ITEM_ID
     * Protected to make immutable.
     * @type int
     */
    protected $id;

    /**
     * @type string
     */
    public $name;

    /**
     * Might not be used at all
     * @type string
     */
    public $category;

    /**
     * @type string
     */
    public $description;

    /**
     * URL/path of the image, not the actual image data itself!
     * @type string
     */
    public $image;

    /**
     * @type float
     */
    public $price;




    /**
     * Constructor
     * 
     * @return ToT\MenuItem
     */
    public function __construct(
        int $id = 0,
        string $name = '',
        string $category = '',
        string $description = '',
        string $image = '',
        float $price = 0
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->category     = $category;
        $this->description  = $description;
        $this->image        = $image;
        $this->price        = $price;
    }



    /**
     * Get the user id
     * @return int
     */
    public function id()
    {
        return $this->id;
    }
    /**
     * Get the username
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    /**
     * Get the user type
     * @return string
     */
    public function category()
    {
        return $this->category;
    }
    /**
     * @return string
     */
    public function description()
    {
        return $this->description;
    }
    /**
     * @return string
     */
    public function image()
    {
        return $this->image;
    }

    public function price()
    {
        return $this->price;
    }

    /**
     * Store the object to the database.
     *
     * @return boolean
     */
    public function save()
    {
        // insert/update the record in the database
        // get a reference to the database
        $db = Database\Connection::getConnection();

        // determine if this is an insert or update
        if ($this->id) {
            $query = 'UPDATE `menu_items`' . PHP_EOL
                . 'SET ' . PHP_EOL
                . '  `name` = "'        . $db->escape_string($this->name)     . '",' . PHP_EOL
                . '  `category` = "'    . $db->escape_string($this->category)         . '",' . PHP_EOL
                . '  `description` = "' . $db->escape_string($this->description) . '",' . PHP_EOL
                . '  `image` = "'       . $db->escape_string($this->image)    . '",' . PHP_EOL
                . '  `price` = "'       . floatval($this->price)     . '"' . PHP_EOL
                . 'WHERE `ITEM_ID` = '  . intval($this->id) . ';';
            if ($db->query($query) === false) {
                throw new Database\Exception('Unable to save user: ' . $db->error . PHP_EOL . '<pre>' . $query . '</pre>');
            }
        } else {
            // TODO: build this out
            $query = 'INSERT INTO `menu_items` (' . PHP_EOL
                . '  `name`,' . PHP_EOL
                . '  `category`,' . PHP_EOL
                . '  `description`,' . PHP_EOL
                . '  `image`,' . PHP_EOL
                . '  `price`' . PHP_EOL
                . ') VALUES (' . PHP_EOL
                . '  "' . $db->escape_string($this->name)     . '",' . PHP_EOL
                . '  "' . $db->escape_string($this->category)         . '",' . PHP_EOL
                . '  "' . $db->escape_string($this->description) . '",' . PHP_EOL
                . '  "' . $db->escape_string($this->image)     . '",' . PHP_EOL
                . '  "' . $db->escape_string($this->price)    . '"' . PHP_EOL
                . ');';
            if ($db->query($query) !== false) {
                // try to read the user id back
                $this->id = $db->insert_id;
            } else {
                throw new Database\Exception('Unable to insert user: ' . $db->error . PHP_EOL . '<pre>' . $query . '</pre>');
            }
        }
        return true;
    }

    /**
     * Load all Menu Items from the database
     * Ordered by category, name, price.
     * @return array
     */
    public static function getAllMenuItems()
    {
        return self::loadMenuItemsByQuery(
            'SELECT * FROM `menu_items` ORDER BY category, name, price;'
        );
    }

    /**
     * Load a MenuItem by id
     *
     * @param int $itemID
     * @return mixed
     */
    public static function getMenuItemByID(int $itemID)
    {
        // secure the query by forcing an integer value
        return self::loadMenuItemByQuery(
            'SELECT * FROM `menu_items` WHERE `ITEM_ID` = ' . intval($itemID) . ';'
        );
    }

    /**
     * Returns an array of all menu items associated to the given orderID
     * @param int $orderID
     * @return MenuItem[]
     */
    public static function getMenuItemsByOrderID(int $orderID)
    {
        return self::loadMenuItemsByQuery(
            "SELECT * FROM `order_menu_items`, `menu_items` WHERE order_menu_items.ORDER_ID = {$orderID} AND order_menu_items.ITEM_ID = menu_items.ITEM_ID;"
        );
    }

    /**
     * Returns an array of all menu items associated to the given orderID and userID
     * @param int $orderID
     * @param int $userID
     * @return MenuItem[]
     */
    public static function getMenuItemsByOrderIDAndUserID(int $orderID, int $userID)
    {
        return self::loadMenuItemsByQuery(
            "SELECT * FROM `order_menu_items`, `menu_items` WHERE order_menu_items.ORDER_ID = {$orderID} AND order_menu_items.ITEM_ID = menu_items.ITEM_ID AND order_menu_items.USER_ID = {$userID};"
        );
    }

    /**
     * Execute a single select
     *
     * @param string $query
     * @return MenuItem
     */
    protected static function loadMenuItemByQuery(string $select)
    {
        // use the multi-select to load matching users
        $items = self::loadMenuItemsByQuery($select);

        // confirm the result set size
        $count = count($items);
        if ($count > 1) {
            // We must have just one result
            throw new Database\Exception('Single MenuItem Lookup Failed: returned ' . count($items) . ' rows.');
        } elseif (!$count) {
            // No results found
            throw new Database\Exception('Single MenuItem Lookup Failed: no match found.');
        }

        // pop off the single result
        return array_shift($items);
    }

    /**
     * Load multiple menu items from a query
     *
     * @param string $query
     * @return array of MenuItem objects
     */
    protected static function loadMenuItemsByQuery(string $select)
    {
        // Setup a dummy return value
        $items = [];

        // Grab a copy of the database connection
        $db = Database\Connection::getConnection();

        // confirm the query worked
        if (($result = $db->query($select)) === false) {
            // TODO: replace a simple error with an exception
            throw new Database\Exception('DB MenuItem Query Failed: ' . $db->error);
        }

        // load and convert each result object
        while (($data = $result->fetch_object()) !== null) {
            $items[] = self::objectForRow($data);
        }

        // convert the resulting object
        return $items;
    }

    /**
     * Convert a MySQL row object to a User
     *
     * @param object
     * @return User
     */
    public static function objectForRow($data)
    {

        // Create the appropriate subclass based on the user type
        $item = new MenuItem(
            $data->ITEM_ID,
            $data->name,
            $data->category,
            $data->description,
            $data->image,
            $data->price
        );
        return $item;
    }
}
