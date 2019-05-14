import 'form-serializer';
import React from 'react';
import ReactDOM from 'react-dom';
import IndexApp from './jsx/IndexApp';

$(document).ready(function(){
    let container = document.getElementById("index-container");
    ReactDOM.render(
        <IndexApp
        />
        , container);
});