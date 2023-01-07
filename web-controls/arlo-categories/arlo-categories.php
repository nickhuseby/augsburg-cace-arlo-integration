<?php

if (!defined('ABSPATH')) {
    exit;
}

function arlo_categories_sc($atts, $content = null) {
    ob_start(); ?>
        <div id="category-list-control">
        </div>  
    
        <script src="//connect.arlocdn.net/jscontrols/1.0/init.js" charset="utf-8" defer="defer"></script>             
        
        <script id="category-list-control-template" type="text/html">
            <a href="<?php echo network_site_url(); ?>/cace/arlo/category?category-id=<%= CategoryID %>&category-name=<%= Name %>">
            <div class="category-name"><%= Name %></div>
            <%= Description.Text %>
            </a>
            <div class="category-children"></div>
        </script>
        <script>
            document.addEventListener("arlojscontrolsloaded", function () {
                var categoryListControl = {
                    moduleType: "CategoryList",
                    targetElement: "#category-list-control",      
                    template: "#category-list-control-template",
                    categoryTreeDepth: 2,
                    customUrls: {
                        category: '<?php echo network_site_url(); ?>/cace/arlo-page/category/'
                    },
                    targetModuleType: "UpcomingEvents",
                };  
                new ArloWebControls().start({
                "platformID": "augsburgcace.arlo.co",
                    "modules": [categoryListControl]
                });
            });
        </script>
    <?php
    return ob_get_clean();
}
add_shortcode('cace_arlo_categories', 'arlo_categories_sc');