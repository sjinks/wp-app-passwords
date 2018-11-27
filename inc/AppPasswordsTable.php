<?php
namespace WildWolf\ApplicationPasswords;

class AppPasswordsTable extends \WP_List_Table
{
    private $user_id;

    public function __construct($args = array())
    {
        $this->user_id = $args['user_id'] ?? 0;
        unset($args['user_id']);
        parent::__construct($args);
    }

    public function prepare_items()
    {
        $this->_column_headers = [
            $this->get_columns(),
            $this->get_sortable_columns(),
            [],
            'name'
        ];

        $this->items = \array_reverse(AppPasswords::get($this->user_id));
    }

    /**
     * @return array
     */
    public function get_columns()
    {
        return [
            'name'      => \__('Name', 'wwapppass'),
            'created'   => \__('Created On', 'wwapppass'),
            'last_used' => \__('Last Used', 'wwapppass'),
            'last_ip'   => \__('Last IP', 'wwapppass'),
        ];
    }

    /**
     * @param array $item
     * @return string
     */
    protected function column_name($item)
    {
        $slug = AppPasswords::slug($item);

        $actions = [
            'revoke' => \sprintf(
                '<button class="button-link hide-if-no-js" data-slug="%1$s">%2$s</button>',
                $slug,
                \__('Revoke', 'wwapppass')
            ),
        ];

        return
              \esc_html($item['name'])
            . $this->row_actions($actions, false)
        ;
    }

    protected function column_created(array $item) : string
    {
        return self::handleDateColumn($item, 'created');
    }

    protected function column_last_used(array $item) : string
    {
        return self::handleDateColumn($item, 'last_used');
    }

    protected function column_last_ip(array $item) : string
    {
        return empty($item['last_ip'])
            ? \__('&mdash;', 'wwapppass')
            : \esc_html($item['last_ip'])
        ;
    }

    private static function handleDateColumn(array $item, string $idx) : string
    {
        return empty($item[$idx])
            ? \__('&mdash;', 'wwapppass')
            : \date(\get_option('date_format', 'r'), $item[$idx])
        ;
    }

    protected function display_tablenav($which)
    {
        echo '<div class="tablenav ', esc_attr($which), '">';
        $this->extra_tablenav($which);
        echo '<br class="clear"/></div>';
    }

    protected function extra_tablenav($which)
    {
        if ('bottom' === $which) {
            echo
                '<div class="alignright hide-if-no-js">',
                    '<button id="ww-app-pass-revoke-all" type="button" class="button-secondary delete">',
                        \__('Revoke all application passwords', 'wwapppass'),
                    '</button>',
                '</div>'
            ;
        }
    }
}
