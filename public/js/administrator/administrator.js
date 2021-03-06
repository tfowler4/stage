var AdministratorEventBinder = function() {
    var activeDiv;
    var currentPageUrl;

    // Display tier details from drop down selection
    $(document).on('change', '#tier-edit-select', function() {
        var tierId     = $(this).val();
        currentPageUrl = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'tier-edit', 'adminpanel-tier':tierId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#tier-edit-content');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display dungeon details from drop down selection
    $(document).on('change', '#dungeon-edit-select', function() {
        var dungeonId  = $(this).val();
        currentPageUrl = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'dungeon-edit', 'adminpanel-dungeon':dungeonId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#dungeon-edit-content');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display encounter details from drop down selection
    $(document).on('change', '#encounter-edit-select', function() {
        var encounterId = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'encounter-edit', 'adminpanel-encounter':encounterId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#encounter-edit-content');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display guild details from drop down selection
    $(document).on('change', '#guild-edit-select', function() {
        var guildId    = $(this).val();
        currentPageUrl = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'guild-edit', 'adminpanel-guild':guildId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#guild-edit-content');
                activeDiv.html(data);
            },
            error:  function(data) {
                console.log(data);
            }
        });
    });

    // Display kill details from drop down selection
    $(document).on('change', '#kill-remove-select', function() {
        var guildId     = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'kill-remove-listing', 'adminpanel-kill-guild-id':guildId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#kill-remove-guild');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display guild encounters from drop down selection
    $(document).on('change', '#kill-edit-guild-select', function() {
        var guildId     = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'kill-edit-listing', 'adminpanel-kill-guild-id':guildId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#kill-edit-guild-content');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display encounter details from drop down selection
    $(document).on('change', '#kill-edit-encounter-select', function() {
        var guildId     = $('#kill-edit-guild-select').val();
        var encounterId = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'kill-edit-listing', 'adminpanel-kill-guild-id':guildId, 'adminpanel-kill-encounter-id' :encounterId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#kill-edit-content');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    /*
    // Submit admin forms
    $(document).on('submit', '.admin-form', function(event) {
        event.preventDefault();

        var formData = new FormData();

        var form       = $(this).closest('form');
        var id         = $(this).prop('id').replace('form-', '');
        currentPageUrl = document.URL;

        var data = $(this).serializeArray();
        $.each(data, function(key, input) {
            formData.append(input.name, input.value);
        });

        if ( form.find("input[name=adminpanel-screenshot]").length > 0 ) {
            var screenshot = form.find("input[name=adminpanel-screenshot]")[0].files[0];
            formData.append('adminpanel-screenshot', screenshot);
        }

        formData.append('request', id);

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    formData,
            encode:  true,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
            },
            error:  function(data) {
                console.log('ERROR');
            }
        });
    });







    // Display guild details from drop down selection
    $(document).on('change', '#admin-select-edit-kill', function() {
        var guildId = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'kill-edit-listing', 'adminpanel-kill-guild-id':guildId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#admin-edit-kill-guild-listing');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display guild details from drop down selection
    $(document).on('change', '.admin-select.kill.remove', function() {
        var guildId = $(this).val();
        currentPageUrl  = document.URL;

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'kill-remove-listing', 'adminpanel-kill-guild-id':guildId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#admin-remove-kill-listing');
                activeDiv.html(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    // Display article details from drop down selection
    $(document).on('change', '.admin-select.article.edit', function() {
        var articleId = $(this).val();
        currentPageUrl  = document.URL;

        tinyMCE.execCommand('mceRemoveEditor', false, 'edit-article'); 

        $.ajax({
            type:    'POST',
            url:     currentPageUrl,
            data:    {request: 'article-edit', 'adminpanel-article':articleId},
            encode:  true,
            success: function(data) {
                activeDiv = $('#admin-article-listing');
                activeDiv.html(data);

                tinyMCE.execCommand('mceAddEditor', false, 'edit-article'); 
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
    */
};