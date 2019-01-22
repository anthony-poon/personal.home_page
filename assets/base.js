global.$ = global.jQuery = require('jquery');
import EntityTable from "./share/entity_table";
import _ from 'underscore';
import fHelper from './share/form_helper'
import "./base.scss";
import "babel-polyfill";
import "@fortawesome/fontawesome-free/js/all"
import "@fortawesome/fontawesome-free/css/all.css"
import 'bootstrap';

$(document).ready(function(){
    // Toggle navbar menu via selector specified in data-submenu attr
    _.each($("table[data-entity-table]"), function(el){
        let table = new EntityTable({
            "el": el
        });
        table.init();

    });

    fHelper.bindDOMElement();
});