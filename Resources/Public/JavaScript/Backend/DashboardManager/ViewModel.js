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
 * Module: TYPO3/CMS/Form/Backend/DashboardManager/ViewModel
 */
define(['jquery',
        'TYPO3/CMS/Backend/Modal',
        'TYPO3/CMS/Backend/Severity',
        'TYPO3/CMS/Backend/Wizard',
        'TYPO3/CMS/Backend/Icons',
        'TYPO3/CMS/Backend/Notification',
        'gridstack',
        'gridstackjqueryui'
        ], function($, Modal, Severity, Wizard, Icons, Notification, gridstack, gridstackjqueryui ) {
        'use strict';

    return (function($, Modal, Severity, Wizard, Icons, Notification, gridstack, gridstackjqueryui ) {

        /**
         * @private
         *
         * @var object
         */
        var _dashboardManagerApp = null;

        /**
         * @private
         *
         * @var object
         */
        var _domElementIdentifierCache = {};

        /**
         * @private
         *
         * @return void
         */
        function _domElementIdentifierCacheSetup() {
            _domElementIdentifierCache = {
                gridStack: { identifier: '[data-identifier="grid-stack"]'},
                widgetContent: { identifier: '[data-identifier="widgetContent"]'},
                newDashboardModalTrigger: {identifier: '[data-identifier="newDashboard"]' },
                newDashboardName: { identifier: '[data-identifier="newDashboardName"]' },
                editDashboardTrigger: { identifier: '[data-identifier="editDashboard"]' },
                newWidgetModalTrigger: {identifier: '[data-identifier="newDashboardWidgetSetting"]' },
                newWidgetType: { identifier: '[data-identifier="newWidgetType"]'},
                refreshWidgetTrigger: { identifier: '[data-identifier="refreshWidget"]'},
            }
        };

        /**
         * @private
         *
         * @return void
         */
        function _editDashboardSetup() {
            $(getDomElementIdentifier('editDashboardTrigger')).on('click', function(e) {
                e.preventDefault();
                document.location = _dashboardManagerApp.getAjaxEndpoint('editDashboard');
            });
        }

        /**
         * @private
         *
         * @return void
         */
        function _newDashboardSetup() {
            $(getDomElementIdentifier('newDashboardModalTrigger')).on('click', function(e) {
                e.preventDefault();
         
                /**
                 * Wizard step 1
                 */
                Wizard.addSlide('new-dashboard-step-1', TYPO3.lang['dashboardManager.newDashboardWizard.step1.title'], '', Severity.info, function(slide) {

                    var html, modal, nextButton;
                    modal = Wizard.setup.$carousel.closest('.modal');
                    nextButton = modal.find('.modal-footer').find('button[name="next"]');

                    html = '<div class="new-form-modal">'
                             + '<div class="form-horizontal">'
                                 + '<div>'
                                     + '<label class="control-label">' + TYPO3.lang['dashboardManager.dashboard_name'] + '</label>'
                                     + '<input class="new-dashboard-name form-control has-error" data-identifier="newDashboardName" />';

                    html +=        '</div>'
                             + '</div>'
                         + '</div>';

                    slide.html(html);
                    $(getDomElementIdentifier('newDashboardName'), modal).focus();

                    $(getDomElementIdentifier('newDashboardName'), modal).on('keyup paste', function(e) {
                        if ($(this).val().length > 0) {
                            $(this).removeClass('has-error');
                            Wizard.unlockNextStep();
                            Wizard.set('dashboardName', $(this).val());
                        } else {
                            $(this).addClass('has-error');
                            Wizard.lockNextStep();
                        }
                    });

                    nextButton.on('click', function() {
                        Wizard.setup.forceSelection = false;
                        Icons.getIcon('spinner-circle-dark', Icons.sizes.large, null, null).done(function(markup) {
                            slide.html($('<div />', {class: 'text-center'}).append(markup));
                        });
                    });
                });

                /**
                 * Wizard step 2
                 */
                Wizard.addSlide('new-dashboard-step-2', TYPO3.lang['dashboardManager.newDashboardWizard.step2.title'], TYPO3.lang['dashboardManager.newDashboardWizard.step2.message'], Severity.info);

                /**
                 * Wizard step 3
                 */
                Wizard.addFinalProcessingSlide(function() {
                    $.post(_dashboardManagerApp.getAjaxEndpoint('create'), {
                        tx_dashboard_system_dashboarddashboardmod1: {
                            dashboardName: Wizard.setup.settings['dashboardName']
                        }
                    }, function(data, textStatus, jqXHR) {
                        document.location = data;
                        Wizard.dismiss();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        Notification.error(textStatus, errorThrown, 2);
                        Wizard.dismiss();
                    });
                }).done(function() {
                    Wizard.show();
                });
            });
        };

        /**
         * @private
         *
         * @return void
         */
        function _newDashboardWidgetSetup() {
            $(getDomElementIdentifier('newWidgetModalTrigger')).on('click', function(e) {
                e.preventDefault();

                var dashboard = _dashboardManagerApp.getDashboard();

                /**
                 * Wizard step 1
                 */
                Wizard.addSlide('new-widget-step-1', TYPO3.lang['dashboardManager.newWidgetWizard.step1.title'], '', Severity.info, function(slide) {

                    var html, modal, nextButton, widgetTypes, widgetTypeSelect;
                    modal = Wizard.setup.$carousel.closest('.modal');
                    nextButton = modal.find('.modal-footer').find('button[name="next"]');

                    html = '<div class="new-form-modal">'
                             + '<div class="form-horizontal">'
                                 + '<div>'

                    // + '<input class="new-widget-type form-control has-error" data-identifier="newWidgetType" />';                                     
                    widgetTypes = _dashboardManagerApp.getAvailableWidgetTypes();
                    Wizard.set('widgetType', widgetTypes[0]['value']);
                    if (widgetTypes.length > 0) {
                        widgetTypeSelect = $('<select class="new-widget-widget-type form-control" data-identifier="newWidgetType" />');
                        for (var i = 0, len = widgetTypes.length; i < len; ++i) {
                            var option = new Option(widgetTypes[i]['label'], widgetTypes[i]['value']);
                            $(widgetTypeSelect).append(option);
                        }
                    }
                    if (widgetTypeSelect) {
                        html +=        '<label class="control-label">' + TYPO3.lang['dashboardManager.widget_type'] + '</label>' + $(widgetTypeSelect)[0].outerHTML;
                    }
                    // console.log(widgetTypes);
                    html +=        '</div>'
                             + '</div>'
                         + '</div>';

                    slide.html(html);
                    
                    Wizard.unlockNextStep();

                    $(getDomElementIdentifier('newWidgetType'), modal).on('change', function(e) {
                        Wizard.set('widgetType', $(getDomElementIdentifier('newWidgetType') + ' option:selected', modal).val());
                    });

                    nextButton.on('click', function() {
                        Wizard.setup.forceSelection = false;
                        Icons.getIcon('spinner-circle-dark', Icons.sizes.large, null, null).done(function(markup) {
                            slide.html($('<div />', {class: 'text-center'}).append(markup));
                        });
                    });
                });

                /**
                 * Wizard step 2
                 */
                Wizard.addSlide('new-widget-step-2', TYPO3.lang['dashboardManager.newDashboardWizard.step2.title'], TYPO3.lang['dashboardManager.newDashboardWizard.step2.message'], Severity.info);

                /**
                 * Wizard step 3
                 */
                Wizard.addFinalProcessingSlide(function() {
                    $.post(_dashboardManagerApp.getAjaxEndpoint('createWidget'), {
                        tx_dashboard_system_dashboarddashboardmod1: {
                            widgetType: Wizard.setup.settings['widgetType'],
                            id: dashboard.id
                        }
                    }, function(data, textStatus, jqXHR) {
                        document.location = data;
                        Wizard.dismiss();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        Notification.error(textStatus, errorThrown, 2);
                        Wizard.dismiss();
                    });
                }).done(function() {
                    Wizard.show();
                });
            });
        };

        /**
         * @public
         *
         * @param string elementIdentifier
         * @param string type
         * @return mixed|undefined
         */
        function getDomElementIdentifier(elementIdentifier, type) {
            _dashboardManagerApp.assert(elementIdentifier.length > 0, 'Invalid parameter "elementIdentifier"', 1477506413);
            _dashboardManagerApp.assert(typeof _domElementIdentifierCache[elementIdentifier] !== "undefined", 'elementIdentifier "' + elementIdentifier + '" does not exist', 1477506414);
            if (typeof type === "undefined") {
                type = 'identifier';
            }

            return _domElementIdentifierCache[elementIdentifier][type] || undefined;
        };

        function _gridStackSetup() {
            $(getDomElementIdentifier('gridStack')).gridstack({
                width: 12,
                alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
                handleClass: 'grid-stack-item-header',
                resizable: {
                    handles: 'se, sw'
                }
            });
            $(getDomElementIdentifier('gridStack')).children('.grid-stack-item').each(function() {
                $(this).fadeIn('slow');
            });
            $(getDomElementIdentifier('gridStack')).on('change', function(event, items) {
                var itemsData = [];
                $(items).each(function(index, item) {
                    itemsData.push({
                        uid: item.id,
                        x: item.x,
                        y: item.y,
                        width: item.width,
                        height: item.height
                    });
                });
                $.post(_dashboardManagerApp.getAjaxEndpoint('change'), {
                    tx_dashboard_system_dashboarddashboardmod1: {
                        items: itemsData
                    }
                }, function(data, textStatus, jqXHR) {
                    Notification.success(
                        TYPO3.lang['dashboardManager.label.dashboard'],
                        TYPO3.lang['dashboardManager.label.layout-saved'],
                        1
                    );console.log('widget');
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    log('change failed');
                    Notification.error(textStatus, errorThrown, 2);
                });
            });
        }

        /**
         * @private
         *
         * @return void
         */
        function _setupWidgetContent() {
            $(getDomElementIdentifier('widgetContent')).each(function() {                
                var widgetId = $(this).data('widgetid');
                updateWidgetContent(widgetId);
            });
        }

        /**
         * @private
         *
         * @return void
         */
        function _refreshWidgetSetup() {
            $(getDomElementIdentifier('refreshWidgetTrigger')).on('click', function(e) {
                e.preventDefault();
                var widgetId = $(this).data('widgetid');
                updateWidgetContent(widgetId);
            });
        }

        /**
         * @private
         *
         * @return void
         */
        function updateWidgetContent(widgetId) {
            var target = $('[data-identifier="widgetContent"][data-widgetid="' + widgetId + '"]');
            if ('object' === $.type(target)) {
                Icons.getIcon('spinner-circle-dark', Icons.sizes.large, null, null).done(function(markup) {
                    $(target).html($('<div />', {class: 'text-center'}).append(markup));
                    $.post(_dashboardManagerApp.getAjaxEndpoint('renderWidget'), {
                        tx_dashboard_system_dashboarddashboardmod1: {
                            widgetId: widgetId
                        }
                    }, function(data, textStatus, jqXHR) {
                        $(target).html(data);
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        Notification.error(textStatus, errorThrown, 2);
                    });
                });
            }
        }

        /**
         * @public
         *
         * @param object dashboardManagerApp
         * @return void
         */
        function bootstrap(dashboardManagerApp) {
            _dashboardManagerApp = dashboardManagerApp;
            _domElementIdentifierCacheSetup();
            _editDashboardSetup();
            _newDashboardSetup();
            _newDashboardWidgetSetup();
            _refreshWidgetSetup();
            _gridStackSetup();
            _setupWidgetContent();
        };

        /**
         * Publish the public methods.
         * Implements the "Revealing Module Pattern".
         */
        return {
            bootstrap: bootstrap,
        };
    })($, Modal, Severity, Wizard, Icons, Notification, gridstack, gridstackjqueryui);
});
