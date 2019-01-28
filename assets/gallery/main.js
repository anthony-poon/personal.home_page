import 'form-serializer';
import React from 'react';
import ReactDOM from 'react-dom';
import GalleryApp from './jsx/GalleryApp';

$(document).ready(function(){
    let container = document.getElementById("gallery-container");
    let url = $(container).data("ajax-url");
    let isLoggedIn = ($(container).data("is-logged-in") == true);
    let signInLink = $(container).data("sign-in-link");
    ReactDOM.render(
        <GalleryApp
            ajaxUrl={url}
            isLoggedIn={isLoggedIn}
            signInLink={signInLink}
        />
    , container);
});