var MSpaceConnector = function() {
    var registrations = [];

    this.register = function(type, callback) {
        registrations.push( {type: type, callback: callback} );
    };

    this.receive = function(message) {
        $.each(registrations, function (index, registration) {
            if( registration.type == message.type ) {
                registration.callback(message);
            }
        });
    };
};
