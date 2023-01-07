<?php
if ( !defined('ABSPATH') ) {
    exit;
}

function arlo_event_sc( $atts, $content=null ) {
    ob_start();
    ?>
        <div id="eventtemplate-control"></div>
        <script
            src="//connect.arlocdn.net/jscontrols/1.1/init.js"
            charset="utf-8"
            defer="defer"
        ></script>
        <script>
            document.addEventListener("arlojscontrolsloaded", function () {
                var eventTemplateControl = {
                    moduleType: "EventTemplate",
                    targetElement: "#eventtemplate-control",
                    queryStringConfig: ["eventtemplate"],
                    template: "#eventtemplate-control-template",
                    callbacks: {
                        onBeforeRender: function (eventTemplateData, jQuery) {
                            console.log("Event Template", eventTemplateData);
                            var heroAlt;
                            eventTemplateData[0].attributes.Description.ContentFields.forEach(field => {
                                if (field.FieldName === "Hero Image Alt Text") {
                                    heroAlt = field.Content.Text.replace('<p>', '').replace('</p>', '');
                                }
                            });
                            heroAlt = (heroAlt === undefined) ? '' : 'alt="' + heroAlt + '"';
                            var heroHTML = `<div class="topimage"><img src="${eventTemplateData[0].attributes.HeroImageUri}" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" ${heroAlt}></div>`;
                            jQuery('h1.entry-title').html(eventTemplateData[0].attributes.Name);
                            jQuery('#main').prepend(heroHTML);
                        }
                    },
                };
                this.state = new ArloWebControls().start({
                    platformID: "augsburgcace.arlo.co",
                    modules: [eventTemplateControl],
                });
            });
        </script>
        <script id="eventtemplate-control-template" type="text/html">
            <div class="event-container">
                    <div class="event-description">
                        <% 
                            var ContentItems = {};
                            ContentItems.learn = Description.ContentFields.filter(c => c.FieldName === "What you'll Learn");
                            ContentItems.desc = Description.ContentFields.filter(c => c.FieldName === "Description");
                            ContentItems.ideal = Description.ContentFields.filter(c => c.FieldName === "Ideal For");
                            ContentItems.feat = Description.ContentFields.filter(c => c.FieldName === "Key Features");
                            ContentItems.canc = Description.ContentFields.filter(c => c.FieldName === "Cancellation Policy");
                            ContentItems.reco = Description.ContentFields.filter(c => c.FieldName === "Related Courses");
                            ContentItems.acin = Description.ContentFields.filter(c => c.FieldName === "Access Instructions");
                        %>
                        <% if (ContentItems.learn.length) { %>
                            <h2><%= ContentItems.learn[0].FieldName %></h2>
                            <%= ContentItems.learn[0].Content.Text %>
                        <% } %>
                        <% if (ContentItems.desc.length) { %>
                            <h2><%= ContentItems.desc[0].FieldName %></h2>
                            <%= ContentItems.desc[0].Content.Text %>
                            <div id="sessions-container"></div>
                            <div class="registration-box"></div>
                        <% } %>
                        <% if (ContentItems.ideal.length) { %>
                            <h2><%= ContentItems.ideal[0].FieldName %></h2>
                            <%= ContentItems.ideal[0].Content.Text %>
                        <% } %>
                        <% if (ContentItems.feat.length) { %>
                            <h2><%= ContentItems.feat[0].FieldName %></h2>
                            <%= ContentItems.feat[0].Content.Text %>
                        <% } %>
                        <div id="presenter-profiles">
                            <h2 class="divider-top">Presenter Profiles</h2>
                        </div>
                        <% if (ContentItems.acin.length) { %>
                            <h2><%= ContentItems.acin[0].FieldName %></h2>
                            <%= ContentItems.acin[0].Content.Text %>
                        <% } %>
                        <% if (ContentItems.canc.length) { %>
                            <div class="registration-box"></div>
                            <h2 class="divider-top"><%= ContentItems.canc[0].FieldName %></h2>
                            <%= ContentItems.canc[0].Content.Text %>
                        <% } %>
                        <%if (ContentItems.reco.length) { %>
                            <h2><%= ContentItems.reco[0].FieldName %></h2>
                            <%= ContentItems.reco[0].Content.Text %>    
                        <% } %>
                    </div>
                    <%= showEventTemplateEventsList({ 
                            template: "#eventtemplatedemo-eventstemplate", 
                            loadImmediately: true,
                            includeLoadMoreButton: true, 
                            loadMoreButtonText: "Load More", 
                            maxCount: 1,
                            filterControlId: 1, 
                            queryStringConfig: ["event"], 
                            callbacks: { 
                                onBeforeRender: function(evt, jQuery){
                                    console.log('Event', evt);
                                    if (evt[0].attributes.Sessions.length > 1) {
                                        jQuery('#sessions-container').attr('data-show', 'true');
                                    }
                                    if (evt[0].attributes.RegistrationIsOpen) {
                                        jQuery('#main').attr('data-registration-open', 'true');
                                    }
                                    jQuery('document').ready((jQuery) => {
                                        if (evt[0].attributes.Presenters) {
                                            evt[0].attributes.Presenters.forEach(async (p, i) => {
                                                let url = 'https://augsburgcace.arlo.co/api/2012-02-01/pub/resources/presenters/' + p.PresenterID;
                                                url = url + '?fields=Profile';
                                                const data = await fetch(url).then(r => r.json());
                                                evt[0].attributes.Presenters[i].Profile = data.Profile;
                                                if (evt[0].attributes.Presenters[i].Profile && evt[0].attributes.Presenters[i].Profile !== undefined) {
                                                    jQuery('document').ready(() => {
                                                        const pres = evt[0].attributes.Presenters[i];
                                                        const profile = `<h3>${pres.Name}</h3>${pres.Profile.ProfessionalProfile.Text}`;
                                                        const presenterProfiles = document.getElementById('presenter-profiles');
                                                        presenterProfiles.innerHTML += profile;
                                                        presenterProfiles.setAttribute('data-state', 'content');
                                                    });
                                                }
                                            });
                                        }
                                    });
                                },
                                onShow: function (_, jQuery) {
                                    if (jQuery('#main').attr('data-registration-open') === 'true') {
                                        jQuery('.course-registration-button').clone().appendTo('.registration-box');
                                    }
                                    if (jQuery('#sessions-container').attr('data-show') === 'true') {
                                        jQuery('#sessions-container').append(jQuery('#sessions'));
                                    } else {
                                        jQuery('#sessions').remove();
                                    }
                                }
                            } 
                        }) %>
            </div>
        </script>
        <script id="presenter-profiles-template" type="text/html">
            <div class="presenter-name">
                <%= FullName %>
            </div>
            <%= Profile %>
        </script>
        <script type="text/template" id="eventtemplatedemo-eventstemplate">
            <div class="event-details">
                <% if (RegistrationIsOpen) { %>
                    <div class="course-registration-button">
                        <a href="<%= RegistrationInfo.RegisterUri %>" class="sidebar-color-box blue-sidebar-box">
                            <%= RegistrationInfo.RegisterMessage %>
                        </a>
                    </div>
                <% } else { %>
                    <div class="course-register-closed">
                        Registration closed
                    </div>
                <% } %>
                <div>
                    <div class="detail-title">Cost</div>
                    <div class="detail-content"><%= AdvertisedOffers[0].OfferAmount.FormattedAmountTaxExclusive %></div>
                </div>
                <div>
                    <div class="detail-title">Location</div>
                    <div class="detail-content">
                        <% if (Location.Name.toLowerCase().includes('online')) { %>
                            <i class="fa fa-laptop" aria-hidden="true"></i>
                        <% } else { %>
                            <i class="fa fa-building" aria-hidden="true"></i>
                        <% } %>
                        <%= Location.Name %>
                    </div>
                </div>
                <div class="arlo-item-header arlo-font-special arlo-text-color-darkest">
                    <div class="detail-title">Start Date</div>
                    <div class="detail-content"><%= formatDate(StartDateTime, "dddd, MMMM Do, YYYY") %></div>
                </div>
                <% if (Sessions && Sessions.length) { %>
                    <div id="sessions">
                        <div class="detail-title">Sessions</div>
                        <ul>
                            <% for (var i = 0; i < Sessions.length; i++) { %>
                                <li>
                                    <%= formatDate(Sessions[i].StartDateTime, "dddd, MMMM Do, YYYY") %>
                                </li>
                            <% } %>
                        </ul>
                    </div>
                <% } %>
                <div>
                    <div class="detail-title"> Duration Details </div>
                    <div class="detail-content"><%= DurationDescription %></div>
                </div>
                <div>
                    <div class="detail-title">End Date</div>
                    <div class="detail-content"><%= formatDate(EndDateTime, "dddd, MMMM Do, YYYY") %></div>
                </div>
                <div>
                    <div class="detail-title">Credits</div>
                    <div class="detail-content">
                        <% if (Credits === "") {%> Non-credit <%}%>
                        <%= Credits %>
                    </div>
                </div>
                <% if (Presenters && Presenters.length) { %>
                    <% if (Presenters.length > 1) { %>
                        <div class="detail-title">Presenters</div>
                    <% } else { %> 
                        <div class="detail-title">Presenter</div>
                    <% } %>
                    <ul class="presenter-list">
                        <% for(var i = 0; i < Presenters.length; i++) { %>
                            <li>
                                <%= Presenters[i].Name %>
                            </li>
                        <% } %>
                    </ul>
                <% } %>
            </div>
        </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'cace_arlo_event', 'arlo_event_sc' ); 