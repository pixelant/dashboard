/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/Dashboard/Backend/DashboardManager
 */
define(['jquery'], function($) {
        'use strict';

    /**
     * Return a static method named "getInstance".
     * Use this method to create the formmanager app.
     */
    return (function() {

        /**
         * @private
         *
         * Hold the instance (Singleton Pattern)
         */
        var _dashboardManagerInstance = null;

        /**
         * @public
         *
         * @param {object} _configuration
         * @param {object} _viewModel
         * @return object
         */
        function DashboardManager(_configuration, _viewModel) {

            /**
             * @private
             *
             * @var bool
             */
            var _isRunning = false;

            /**
             * @public
             *
             * @param {mixed} test
             * @param {string} message
             * @param {int} messageCode
             * @return void
             */
            function assert(test, message, messageCode) {
                if ('function' === $.type(test)) {
                    test = (test() !== false);
                }
                if (!test) {
                    message = message || "Assertion failed";
                    if (messageCode) {
                        message = message + ' (' + messageCode + ')';
                    }
                    if ('undefined' !== typeof Error) {
                        throw new Error(message);
                    }
                    throw message;
                }
            }

            /**
             * @public
             *
             * @return object
             */
            function getAvailableWidgetTypes() {
                var widgetTypes = [];

                if ('array' === $.type(_configuration['selectableWidgetTypesConfiguration'])) {
                    for (var i = 0, len = _configuration['selectableWidgetTypesConfiguration'].length; i < len; ++i) {
                        widgetTypes.push({
                            label: _configuration['selectableWidgetTypesConfiguration'][i]['0'],
                            value: _configuration['selectableWidgetTypesConfiguration'][i]['1']
                        });
                    }
                }
                return widgetTypes;
            }

            /**
             * @public
             *
             * @return object
             */
            function getDashboard() {
                var dashboard = [];
                if ('object' === $.type(_configuration['dashboard'])) {
                    dashboard = _configuration['dashboard'];
                }
                return dashboard;
            }

            /**
             * @public
             *
             * @param {string} endpointName
             * @return object
             * @throws 1477506508
             */
            function getAjaxEndpoint(endpointName) {
                assert(typeof _configuration['endpoints'][endpointName] !== 'undefined', 'Endpoint ' + endpointName + ' does not exist', 1477506508);

                return _configuration['endpoints'][endpointName];
            }

            /**
             * @private
             *
             * @return void
             * @throws 1475942906
             */
            function _viewSetup() {
                assert('function' === $.type(_viewModel.bootstrap), 'The view model does not implement the method "bootstrap"', 1475942906);
                _viewModel.bootstrap(_dashboardManagerInstance);
            }

            /**
             * @private
             *
             * @return void
             * @throws 1477506504
             */
            function _bootstrap() {
                _configuration = _configuration || {};
                assert('object' === $.type(_configuration['endpoints']), 'Invalid parameter "endpoints"', 1477506504);
                _viewSetup();
            };

            /**
             * @public
             *
             * @return TYPO3/CMS/Dashboard/Backend/DashboardManager
             * @throws 1475942618
             */
            function run() {
                if (_isRunning) {
                    throw 'You can not run the app twice (1475942618)';
                }
                _bootstrap();
                _isRunning = true;
                return this;
            };

            /**
             * Publish the public methods.
             * Implements the "Revealing Module Pattern".
             */
            return {
                getAjaxEndpoint: getAjaxEndpoint,
                getAvailableWidgetTypes: getAvailableWidgetTypes,
                getDashboard: getDashboard,
                assert: assert,
                run: run
            };
        };

        /**
         * Emulation of static methods
         */
        return {
            /**
             * @public
             * @static
             *
             * Implement the "Singleton Pattern".
             *
             * Return a singleton instance of a
             * "DashboardManager" object.
             *
             * @param object configuration
             * @param object viewModel
             * @return object
             */
            getInstance: function(configuration, viewModel) {
                if(_dashboardManagerInstance === null) {
                    _dashboardManagerInstance = new DashboardManager(configuration, viewModel);
                }
                return _dashboardManagerInstance;
            }
        };
    })();
});
