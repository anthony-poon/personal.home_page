import 'form-serializer';
import React from 'react';
import ReactDOM from 'react-dom';
import GalleryApp from './jsx/GalleryApp';
import GalleryModal from "./jsx/GalleryModal";

$(document).ready(function(){
    let container = document.getElementById("gallery-container");
    let modalContainer = document.getElementById("gallery-modal-container");
    let url = $(container).data("ajax-url");
    ReactDOM.render(
        <GalleryApp
            ajaxUrl={url}
        />
    , container);
    ReactDOM.render(
        <GalleryModal
            ajaxUrl={url}
        />
    , modalContainer)
});