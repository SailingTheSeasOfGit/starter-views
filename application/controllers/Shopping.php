<?php

class Shopping extends Application {

    function __construct() {
        parent::__construct();
    }

    public function index() {

        // pictorial menu
        $count = 1;
        foreach ($this->categories->all() as $category) {
            $chunk = 'category' . $count;
            $this->data[$chunk] = $this->parser->parse('category-shop', $category, true);
            foreach ($this->menu->all() as $menuitem) {
                if ($menuitem->category != $category->id)
                    continue;
                $this->data[$chunk] .= $this->parser->parse('menuitem-shop', $menuitem, true);
            }
            $count++;
        }

        $stuff = file_get_contents('../data/receipt.md');
        $this->data['receipt'] = $this->parsedown->parse($stuff);
        $this->data['content'] = '';
        $this->render('template-Shopping');
    }

}
