// -------------------------------------
// Social Login APP
// -------------------------------------
var $ = jQuery;
var WP_Social_Login = {
  elm: {
    status: $(".wpsl_status"),
  },
  settings: {
    debug: parseInt(wpsl_options.debug_mode),
  },
  facebook: {
    api: wpsl_options.fb_app_id,
    isActive: false,
    elm: {
      loginBtn: $(".wpsl_btn_facebook"),
      loading: $(".wpsl_loading_facebook"),
    },
  },
  google: {
    api: wpsl_options.google_api,
    elm: {
      loginBtn: $(".wpsl_btn_google"),
      loading: $(".wpsl_loading_google"),
    },
  },

  // debugLog
  debugLog: function (msg) {
    if (this.settings.debug) {
      console.log(msg);
    }
  },

  init: function () {
    this.debugLog("WP Social Login Initialized");

    if ( ! this.facebook.api.length ) {
      this.facebook.elm.loginBtn.remove();
    }

    if ( ! this.google.api.length ) {
      this.google.elm.loginBtn.remove();
    }

    this.disableButton(this.facebook.elm.loginBtn);
    this.disableButton(this.google.elm.loginBtn);

    var prevent_default = function (e) {
      e.preventDefault();
    };

    $(this.facebook.elm.loginBtn).on("click", prevent_default);
    $(this.google.elm.loginBtn).on("click", prevent_default);
  },

  // facebookApiInit
  facebookApiInit: function () {
    this.debugLog("Facebook Login: API is initializing");
    var self = this;
    var login_btn = this.facebook.elm.loginBtn;

    // Connect to Facebook API if API key is present
    if (this.facebook.api.length > 0) {
      window.fbAsyncInit = function () {
        self.debugLog("Facebook Login: API connected successfully");
        FB.init({
          appId: self.facebook.api,
          status: false,
          cookie: true,
          xfbml: true,
          version: "v4.0",
        });

        self.enableButton(login_btn);
      };
    } else {
      this.debugLog("Facebook Login: API key is missing");
      this.disableButton(login_btn);
    }

    // Login to Facebook on click on button
    login_btn.on("click", function (e) {
      e.preventDefault();
      if (self.facebook.isActive) {
        return;
      }
      self.disableButton(login_btn);
      self.facebook.isActive = true;
      self.loginToFacebook();
    });
  },

  // loginToFacebook
  loginToFacebook: function () {
    if (typeof FB === "undefined") {
      this.debugLog("Facebook Login: API is not loaded yet");
    }
    if ( ! this.facebook.api.length ) {
      this.facebook.isActive = false;
      this.showStatus("failed");
      this.enableButton(this.facebook.elm.loginBtn);

      this.debugLog("Facebook Login: API key is missing");
      return;
    }
    var self = this;
    FB.login(
      function (response) {
        self.facebookLoginCallback(response);
      },
      { scope: "public_profile,email" }
    );
  },

  // facebookLoginCallback
  facebookLoginCallback: function (response) {
    if (response.status !== "connected") {
      this.facebook.isActive = false;
      this.enableButton(this.facebook.elm.loginBtn);
      this.debugLog("Facebook Login: Connection failed");
      return;
    }

    var self = this;
    self.isLoading("facebook", true);

    FB.api(
      "/me?fields=id,name,first_name,last_name,email,picture.type(large)",
      function (userData) {
        if (!userData) {
          self.showStatus("failed");
          this.debugLog("Facebook Login: Connection failed");
          return;
        }

        var data = {
          api: "Facebook",
          profile_id: userData.id,
          full_name: userData.name,
          first_name: userData.first_name,
          last_name: userData.last_name,
          email: userData.email,
          profile_picture: userData.picture.data.url,
        };
        self.sendLoginRequest(data, self.facebook);
      }
    );
  },

  // googleApiInit
  googleApiInit: function () {
    this.debugLog("Google Login: API is initializing");
    var login_btn = this.google.elm.loginBtn;
    this.enableButton(login_btn);

    if (!this.google.api.length) {
      this.debugLog("Google Login: API is missing");
      this.disableButton(login_btn);
      return;
    }

    var self = this;
    gapi.load("auth2", function () {
      gapi.auth2.init({
        client_id: self.google.api,
      });

      login_btn.each(function (i, el) {
        $(el).on("click", function (e) {
          e.preventDefault();
        });

        gapi.auth2
          .getAuthInstance()
          .attachClickHandler(el, {}, function (user) {
            self.isLoading("google", true);
            self.debugLog("Google Login: Singin is processing...");
            var currentUser = gapi.auth2.getAuthInstance().currentUser.get();
            var profile = currentUser.getBasicProfile();

            var data = {
              api: "Google",
              profile_id: profile.getId(),
              full_name: profile.getName(),
              first_name: profile.getGivenName(),
              last_name: profile.getFamilyName(),
              email: profile.getEmail(),
              profile_picture: profile.getImageUrl(),
            };

            self.sendLoginRequest( data, self.google );
          });
      });
    });
  },

  // sendLoginRequest
  sendLoginRequest: function ( data, referrer ) {
    this.debugLog(data.api + " Singin: Sending signin request to server...");

    var self = this;
    var formData = {
      action: "wpsl_login_request_handler",
      profile_id: data.profile_id,
      email: data.email,
      full_name: data.full_name,
      first_name: data.first_name,
      last_name: data.last_name,
      profile_picture: data.profile_picture,
    };

    $.ajax({
      type: "post",
      dataType: "json",
      url: wpsl_options.ajax_url,
      data: formData,
      success: function (response) {
        console.log( response );
        if (response.status) {
          self.debugLog(
            data.api + "Singin: Singin is successful, redirecting..."
          );
          self.showStatus("success");
          // window.location.href = response.redirect_url;
        } else {
          self.showStatus("failed");
          self.isLoading("facebook", false);
          self.isLoading("google", false);
          self.enableButton( referrer.elm.loginBtn );
        }
      },
      error: function (error) {
        console.log( error );
        self.showStatus("failed");
        self.isLoading("facebook", false);
        self.isLoading("google", false);
        self.debugLog(data.api + "Singin: Singin is failed");
        self.enableButton( referrer.elm.loginBtn );
      },
    });
  },

  showStatus: function (status_type) {
    var msg, type;
    switch (status_type) {
      case "success":
        msg = wpsl_options.success_msg;
        type = "status-success";
        break;
      case "failed":
        msg = wpsl_options.error_msg;
        type = "status-failed";
        break;
      case "wait":
        msg = wpsl_options.wait_msg;
        type = "status-warning";
        break;
      default:
        msg = "";
        type = "";
        break;
    }

    this.elm.status.html(
      "<span class=" + status + "-" + type + ">" + msg + "</span>"
    );
  },

  isLoading: function (type, status) {
    if (!this[type]) {
      return;
    }
    if (status) {
      this[type].elm.loading.addClass("azbdp--show");
      return;
    }

    this[type].elm.loading.removeClass("azbdp--show");
  },

  enableButton: function (button) {
    button.prop("disabled", false);
  },

  disableButton: function (button) {
    button.prop("disabled", true);
  },
};

// Init
WP_Social_Login.init();

// Facebook Login
WP_Social_Login.facebookApiInit();

// Google Login
function initGAPI() {
  WP_Social_Login.debugLog("Google Login: API connected successfully");
  WP_Social_Login.googleApiInit();
}