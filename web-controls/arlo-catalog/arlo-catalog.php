<?php
 if ( !defined('ABSPATH') ) {
    exit;
 }

 function arlo_catalog_sc($atts, $content = null ) {
    $args = shortcode_atts(array(
        "category" => null,
        "category_name" => null,
        "filtering" => "no"
    ), $atts, 'arlo_catalog');
    if ($args["category"] === "url") {
        $args["category"] = get_query_var('category-id');
    }
    if ($args["category_name"] === "url") {
        $args["category_name"] = get_query_var('category-name');
    }
    ob_start(); ?>
        <div id="filters"></div>
        <div id="upcoming-events-control"></div>
            
        <script src="//connect.arlocdn.net/jscontrols/1.1/init.js" charset="utf-8" defer="defer"></script>     
        
        <script id="upcoming-events-control-template" type="text/html">
            <div>
                <div class="course-header">
                    <a class="course-title-link" href="<%= ViewUri %>"><h2 class="course-title"><%= Name %></h2></a>
                </div>
                <img src="<%= ListImageUri %>" alt="Image from <% if (Categories.length > 0) {%><%=Categories[0].Name%> <%}%>course">
                <div class="course-body">
                    <div class="course-location"><% if (Location.Name.toLowerCase().includes("online")) { %> <i class="fa fa-laptop" aria-hidden="true"></i> <% } else { %><i class="fa fa-building" aria-hidden="true"></i> <% } %><%= Location.Name %></div>
                    <% if (Categories.length > 0) { %>
                        <div class="course-categories">
                            <% for (var i = 0; i < Categories.length; i++) { 
                                var category = Categories[i];    
                            %>
                                <a href="/cace/arlo-page/category/?category-id=<%= category.CategoryID %>&category-name=<%= category.Name %>"><%= category.Name %> </a>
                            <% } %>
                        </div>
                    <% } %>
                    <div class="course-summary"><%= Summary %></div>
                    <div class="course-actions">
                        <div class="course-register"><% if (RegistrationIsOpen) { %> <a class="sidebar-color-box maroon-sidebar-box" href="<%= RegistrationInfo.RegisterUri %>"><%= RegistrationInfo.RegisterMessage %></a><% } else { %> Registration Closed <% } %></div>
                        <div class="course-link"><a class="sidebar-color-box blue-sidebar-box" href="<%= ViewUri %>">Learn More</a></div>
                    </div>
                </div>
                <div class="course-footer">
                    <div class="course-info">
                        <div class="course-date">
                            <%= SmartDateFields.startMonth %> 
                            <%= SmartDateFields.startDay %>
                            <% if (SmartDateFields.endMonth || SmartDateFields.endDay) { %> - <% } %>
                            <%= SmartDateFields.endMonth %> <%= SmartDateFields.endDay.replace('-', '') %></div>
                        <div class="course-credit"><% if (Credits === "") { %>Non-credit<% } else { %><%= Credits %><% } %></div>
                        <div class="course-cost"><%= AdvertisedOffers[0].OfferAmount.FormattedAmountTaxInclusive %></div>
                    </div>
                </div>
            </div>
        </script>

        <script type='text/template' id='filter-template'>
            <div>
                <div>Filter by Location:</div>
                <%= showFilter({
                    filterCode: "locname",
                    displayStyle: "checkboxes"
                }) %>
            </div>
            <div>
                <div>Filter by Category:</div>
                <%= showFilter({
                    filterCode: "templatecategory",
                    displayStyle: "checkboxes"
                }) %>
            </div>
        </script>
        
        <script>
            document.addEventListener("arlojscontrolsloaded", function () {
                var filterControl = {
                    moduleType: "Filters",
                    targetElement: "#filters",    
                    template: "#filter-template",                   
                    filterControlId: "myFilter"                    
                }; 
                var upcomingEventsControl = {
                    moduleType: "UpcomingEvents",
                    targetElement: "#upcoming-events-control",
                    template: "#upcoming-events-control-template",
                    filterControlId: "myFilter",
                    callbacks: {},
                    maxCount: 999,
                    customUrls : {
                        eventtemplate: '<?php echo network_site_url(); ?>/cace/arlo-page/event'
                    }
                };
                <?php if ($args["category"] !== null) { ?>
                upcomingEventsControl.filter = {
                    templateCategoryId: <?php echo $args["category"]; ?>
                }
                <?php } ?>
                <?php if ($args["category_name"] !== null ) { ?>
                    upcomingEventsControl.callbacks.onBeforeRender = function(evt, jQuery) {
                        jQuery(".entry-title").html('Upcoming Courses in <?php echo $args["category_name"]; ?>');
                    }
                <?php } ?>
                var modules = [upcomingEventsControl];
                <?php if ($args["filtering"] === "yes") { ?>
                    modules.push(filterControl);
                <?php } ?>
                new ArloWebControls().start({
                    "platformID": "augsburgcace.arlo.co",
                    "modules": modules
                });
            });
        </script>
    <?php
    return ob_get_clean();
}
add_shortcode('cace_arlo_catalog', 'arlo_catalog_sc');