<form role="search" method="get" class="playbook-search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" class="search-field" 
           placeholder="<?php esc_attr_e('Поиск по плейбуку...', 'playbook'); ?>" 
           value="<?php echo get_search_query(); ?>" 
           name="s"
           aria-label="<?php esc_attr_e('Поиск по плейбуку', 'playbook'); ?>">
    <button type="submit" class="search-button">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#5F6368" stroke-width="2"/>
            <path d="M21 21L16.65 16.65" stroke="#5F6368" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>
</form>