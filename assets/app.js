import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/app.scss';

import $ from 'jquery';

import Routing from "../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
import routes from "../public/js/fos_js_routes.json";
import DataTable from 'datatables.net-dt';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

Routing.setRoutingData(routes);

export default Routing;
