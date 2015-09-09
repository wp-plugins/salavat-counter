<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class salavatcounter_lists extends WP_List_Table
{

    public $table_data = array() , $per_page = 3 , $all_rows_num , $tbl_start = 0;
    function __construct() {
        global $status, $page , $wpdb;

        parent::__construct( array(
            'singular' => 'salavat', /*singular name of the listed records*/
            'plural' => 'salavats', /*plural name of the listed records*/
            'ajax' => false /*does this table support ajax?*/

        ) );
        $this->per_page = 50;
    }


    function no_items() {
        _e('No Item Found' , 'salavatcounter');
    }

    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
            case 'title':
            case 'view_method':
            case 'codes':
                return $item[ $column_name ];
            default:
                //return print_r( $item, true ) ; /*Show the whole array for troubleshooting purposes*/
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            array('id' , false),
            array('title' , false),
            array('view_method' , false),
            array('codes' , false),
        );
        return $sortable_columns;
    }

    function get_columns() {
        $columns = array(
            'id' => __('id' , 'salavatcounter'),
            'title' => __('title' , 'salavatcounter'),
            'view_method' => __('view method' , 'salavatcounter'),
            'codes' => __('codes' , 'salavatcounter'),

        );
        return $columns;
    }

    function usort_reorder( $a, $b ) {
        /* If no sort, default to title*/
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'ID';
        /* If no order, default to asc*/
        $order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';
        /* Determine sort order*/
        $result = strcmp( $a[$orderby], $b[$orderby] );
        /* Send final sort direction to usort*/
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_title( $item ) {
        global $wpdb;
        $salavatcounter =   $wpdb->prefix.'salavatcounter';
        $status         =   $wpdb->get_col("select `status` from $salavatcounter where id=".$item['id']);
        $status = $status[0];
        $actions = array(
            'edit'      => sprintf( '<a href="?page=%s&action=%s&salavat=%s">'.__('Edit' , 'salavatcounter').'</a>', $_REQUEST['page'], 'edit', $item['id'] ),
            'delete'    => sprintf( '<a class="confirmdelete" href="?page=%s&action=%s&salavat=%s">'.__('Delete' , 'salavatcounter').'</a>', $_REQUEST['page'], 'delete', $item['id'] ),
        );

        if($status == 1){
            $actions['deactive'] = sprintf( '<a  href="?page=%s&action=%s&salavat=%s">'.__('Deactive' , 'salavatcounter').'</a>', $_REQUEST['page'], 'deactive', $item['id'] );

        }elseif($status  == 0){
            $actions['active'] =  sprintf( '<a  href="?page=%s&action=%s&salavat=%s">'.__('Active' , 'salavatcounter').'</a>', $_REQUEST['page'], 'active', $item['id'] );

        }

        return sprintf( '%1$s %2$s', $item['title'], $this->row_actions( $actions ) );
    }


    function column_view_method( $item ) {
        if($item['view_method'] == 'corner'){
            global $wpdb;
            $salavatcounter =$wpdb->prefix.'salavatcounter';
            $show_corner = $wpdb->get_col("select show_corner from $salavatcounter where id=".$item['id']);
            $show_corner = $show_corner[0];
              return '
                <label for="salavatcounter-view-on-my-site-'.$item['id'].'">'.__('Method is corner , view on my site' , 'salavatcounter').'</label>
                <input type="checkbox" data-id="'.$item['id'].'" id="salavatcounter-view-on-my-site-'.$item['id'].'" class="salavatcounter-view-on-my-site" '.checked( $show_corner, 1, false ).' />
                <img src="'._salavatcounter_PATH.'/css/img/ajax-loader.gif" class="salavat-ajax-loader">';
        }elseif( $item['view_method'] == 'widget'){
            return sprintf(__('use <a href="%s">widgets</a> or shortcode' , 'salavatcounter') , get_bloginfo('siteurl').'/wp-admin/widgets.php');
        }

    }


    function single_row( $a_comment ) {

        global $wpdb;
        $salavatcounter =   $wpdb->prefix.'salavatcounter';
        $status         =   $wpdb->get_col("select `status` from $salavatcounter where id=".$a_comment['id']);
        $status = $status[0];


        if( $status == 1)
        {
            $the_comment_class = 'status-active';
        }
        if( $status == 0)
        {
            $the_comment_class = 'status-deactive';
        }


        echo "<tr id='row-".$a_comment->id."' class='".$the_comment_class."'>";
        echo $this->single_row_columns( $a_comment );
        echo "</tr>\n";
    }

    function prepare_items() {
        global $wpdb;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $per_page =  $this->per_page;
        $current_page = $this->get_pagenum();
        /*populate table*/
        $fetch_query 		=	'SELECT * FROM '.$wpdb->prefix.'salavatcounter';
        $row_count_query 	=	'SELECT count(*) as cnt FROM '.$wpdb->prefix.'salavatcounter';
        $this->tbl_start = ( $current_page-1 )* $per_page;
        $fetch_query 	.=	' ORDER BY id DESC LIMIT '.$this->tbl_start.','.$this->per_page;
        $rows 					= $wpdb->get_results($fetch_query);
        $all_rows_num 			= $wpdb->get_results($row_count_query);
        $this->all_rows_num 	= $all_rows_num[0]->cnt;
        foreach ($rows as $row) {
            $short_code = __('short code' , 'salavatcounter');
            $theme_code = __('theme code' , 'salavatcounter');
            $use_in_other_sites= __('use in other sites' , 'salavatcounter');
            $id     = $row->id;
            $mode   = $row->view_method;
            $site_url = get_site_url();
            $site_url =  rtrim( $site_url, '/' );
            $site_url .= '/';
            $codes = <<<ALI
            <input type="text" class="salavatcounter-hide-me" >
            <span class="salavat-codes"><strong>$short_code</strong><input type="text"  value="[salavat_counter id='$id']" /> </span>
            <span class="salavat-codes"><strong>$theme_code</strong><input type="text"  value="<?php echo do_shortcode('[salavat_counter id='$id']' ); ?>" /> </span>
            <span class="salavat-input-methos-$mode salavat-codes"><strong>$use_in_other_sites</strong><input type="text"  value="<script type='text/javascript' src='$site_url?salavat-counter-js=$id&mode=$mode'></script>" /></span>
ALI;
            $title = '
            <strong>'.$row->name.'</strong>
            <br>
            '.$row->niyat;

            $title .= '<hr><span class="salavat-span">'.$row->salavat.'</span>';


            $this->items[] =array(
                'id' => $row->id,
                'title' => $title,
                'view_method' => $row->view_method,
                'codes' => stripslashes($codes),
            ) ;
        }


        $this->set_pagination_args( array(
            'total_items' => $this->all_rows_num,
            'per_page' => $this->per_page
        ) );

    }

} /*////#class////*/
