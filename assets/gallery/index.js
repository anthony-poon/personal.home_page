import 'form-serializer';
import React from 'react';
import ReactDOM from 'react-dom';
import GalleryApp from './jsx/GalleryApp';

$(document).ready(function(){
    let container = document.getElementById("gallery-container");
    let url = $(container).data("ajax-url");
    ReactDOM.render(
        <GalleryApp
            ajaxUrl={url}
        />
    , container);
});