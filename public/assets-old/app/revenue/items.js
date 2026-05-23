/**
 * Created by degre on 2/12/2021.
 */
//https://stackoverflow.com/questions/21096141/jstree-and-context-menu-modify-items
    //https://gist.github.com/pontikis/1097570    -jstree Action Events
//https://everyething.com/Example-of-jsTree-with-different-context-menu-for-different-node-type

var IRate = function () {
        var treeDiv = $('#contextmenu');
        var rateModal = $("#rate-modal");
        var rateForm = $("#rate-form");
        var rateEditForm = $("#rate-edit-form");
        var rateEditModal = $("#rate-edit-modal");
        var propClassModal = $("#prop-class-modal");
        var propClassForm = $("#prop-class-form");
        var templateModal = $("#template-modal");
        var templateForm = $("#template-form");
        var statusModal = $("#status-modal");
        var statusForm = $("#status-form");
        var pcuModal = $('#prop-class-use-create-modal');
        var pcuForm = $("#pcu-form");
        return {
            init: function () {
                try {
                    IRate.validateRateForm();
                    IRate.validatePCUForm();
                    IRate.validateRateUpdateForm();
                    IRate.validatePropClassForm();
                    IRate.validateRateForm();
                    IRate.validateTemplateForm();
                    IRate.validateStatusForm();
                    IRate.initTree([]);
                    IRate.loadRates();
                } catch (e) {
                    console.log(e);
                }

                $("#btn-add-rate").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.onPropClassUseCreate(null);
                });
                $("#btn-save-rate-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.createRate();
                });
                $("#btn-save-pcu-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.createPCURate();
                });
                $("#btn-edit-rate-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.editRate();
                });
                $("#btn-save-class-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.editPropClassUse();
                });
                $("#btn-template-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.editTemplatePath();
                });
                $("#btn-status-action").on('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    IRate.editPropClassUseStatus();
                });
            },
            customMenu: function ($node) {
                var items = {
                    "Create": {
                        "label": "Create Property Category",
                        "action": function (obj) {
                            IRate.onCreate($node);
                        }
                    },
                    "CreatePCU": {
                        "label": "Create Revenue Item",
                        "action": function (obj) {
                            IRate.onPropClassUseCreate($node);
                        }
                    },
                    "Rate": {
                        "seperator_before": false,
                        "seperator_after": false,
                        "label": "Edit Rate Information",
                        action: function (obj) {
                            IRate.onEdit($node);
                        }
                    },
                    "PropClassUse": {
                        "seperator_before": false,
                        "seperator_after": false,
                        "label": "Edit Information",
                        action: function (obj) {
                            IRate.onPropClassEdit($node);
                        }
                    },
                    "Template": {
                        "seperator_before": false,
                        "seperator_after": false,
                        "label": "Edit Template Path",
                        action: function (obj) {
                            IRate.onTemplateEdit($node);
                        }
                    },
                    "Status": {
                        "seperator_before": false,
                        "seperator_after": false,
                        "label": "Edit Revenue Item Status",
                        action: function (obj) {
                            IRate.onStatusEdit($node);
                        }
                    }
                    // "Edit": {
                    //     "separator_before": false,
                    //     "separator_after": true,
                    //     "label": "Edit",
                    //     "action": false,
                    //     "submenu": {
                    //
                    //
                    //
                    //
                    //     }
                    // }
                };

                console.log($node);
                //If node is a folder do not show the "delete" menu item
                if ($($node).hasClass("jstree-closed") || $($node).hasClass("jstree-open") || $node.original.parentid == 0 || $node.children.length > 0) {
                    delete items.Rate;
                    delete items.Template;
                    delete items.Status;
                    delete items.CreatePCU;
                    // if ($node.children.length > 0) {
                    //     var nodeId = $node.children_d[0];
                    //     var node = $.jstree.reference(treeDiv).get_node(nodeId);
                    //     var rate = node.original;
                    //     if (rate.flatrate <= 0 && rate.rateimpost <= 0 && rate.maxamount < 0 && rate.minamount < 0) {
                    //         delete items.CreatePCU;
                    //     }
                    //
                    // }
                } else {
                    delete items.Create;

                }

                return items;
            },
            initTree: function (treeData) {
                treeDiv.jstree({
                    'core': {
                        'check_callback': true,
                        'themes': {
                            'responsive': false
                        },
                        'data': treeData
                    },
                    'types': {
                        'default': {
                            'icon': 'icofont icofont-document-folder  font-theme'
                        },
                        'file': {
                            'icon': 'icofont icofont-file-alt font-dark'
                        }
                    },
                    "plugins": [
                        "contextmenu", "dnd", "search",
                        "state", "types", "wholerow"
                    ],
                    "contextmenu": {
                        "items": IRate.customMenu
                    }
                });


                // do something on node selected
                treeDiv.bind("select_node.jstree", function (event, data) {

                    var node = $(event.target).closest("li");
                    // var node_id = node[0].id; //id of the selected node
                    //  console.log(data.node.original);
                    var checkedValue = data.node.text;
                    var isParent = data.instance.is_parent();
                    // do something
                });
                treeDiv.on('show_contextmenu.jstree', function (e, reference, element) {
                    // if (reference.node.parents.length !== 1) {
                    //     //alert('is child');
                    //
                    // } else {
                    //     $('.vakata-context').remove();
                    // }

                });
            },
            resetRateForm: function () {
                $('input[name="parentId"]', rateForm).remove();
                rateForm.resetForm();
                rateForm.clearForm();
                $("#isRated").val('').trigger('change');
                $("#isApplicable").val('').trigger('change');
            },
            resetPCUForm: function () {
                $('input[name="parentId"]', pcuForm).remove();
                pcuForm.resetForm();
                pcuForm.clearForm();
            },
            onCreate: function (node) {
                var parentId = (node !== null) ? node.original.id : 0;
                IRate.resetRateForm();
                rateForm.append($('<input>').attr('type', 'hidden').attr('name', 'parentId').val(parentId));
                rateModal.modal('show');
            },
            onPropClassUseCreate: function (node) {
                var parentId = (node !== null) ? node.original.id : 0;
                IRate.resetPCUForm();
                pcuForm.append($('<input>').attr('type', 'hidden').attr('name', 'parentId').val(parentId));
                pcuModal.modal('show');
            },
            onEdit: function (node) {
                var rate = node.original;
                $(".rateName").val(rate.name);
                $(".rateImpost").val(rate.rateimpost);
                $(".flatRate").val(rate.flatrate);
                $(".minAmount").val(rate.minamount);
                $(".maxAmount").val(rate.maxamount);
                $(".isRated").val(((rate.israted) ? 'yes' : 'no')).trigger('change');
                $('input[name="rateId"]', rateEditForm).remove();
                rateEditForm.append($('<input>').attr('type', 'hidden').attr('name', 'rateId').val(rate.id));
                rateEditModal.modal('show');
            },
            onPropClassEdit: function (node) {
                var rate = node.original;
                $(".rateName").val(rate.name);
                $(".rateCode").val(rate.code);
                $(".rateStartDate").val(rate.startgeneration);
                $(".description").val(rate.descn);
                $('input[name="parentId"]', propClassForm).remove();
                $('input[name="clazzId"]', propClassForm).remove();
                propClassForm.append($('<input>').attr('type', 'hidden').attr('name', 'parentId').val(rate.parentid));
                propClassForm.append($('<input>').attr('type', 'hidden').attr('name', 'clazzId').val(rate.id));
                propClassModal.modal('show');
            },
            onTemplateEdit: function (node) {
                var rate = node.original;
                $(".pathName").val(rate.templatepath);
                $('input[name="ids\[\]"]', templateForm).remove();
                templateForm.append($('<input>').attr('type', 'hidden').attr('name', 'ids[]').val(rate.id));
                templateModal.modal('show');
            },
            onStatusEdit: function (node) {
                var rate = node.original;
                if (rate.isapplicable) {
                    $("#applicable").prop('checked', true);
                    $("#napplicable").prop('checked', false);
                } else {
                    $("#applicable").prop('checked', false);
                    $("#napplicable").prop('checked', true);
                }
                $('input[name="clazzId"]', statusForm).remove();
                statusForm.append($('<input>').attr('type', 'hidden').attr('name', 'clazzId').val(rate.id));
                statusModal.modal('show');
            },
            createRate: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: rateForm.attr('data-action'),
                    data: rateForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (rateForm.valid()) {
                            Common.showSpinner(rateForm, "Saving Rate...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(rateForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(rateForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(rateForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            createPCURate: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: pcuForm.attr('data-action'),
                    data: pcuForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (pcuForm.valid()) {
                            Common.showSpinner(pcuModal, "Saving Property Class Use...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(pcuModal);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(pcuModal);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(pcuForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            editRate: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: rateEditForm.attr('data-action'),
                    data: rateEditForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (rateEditForm.valid()) {
                            Common.showSpinner(rateEditForm, "Saving Rate...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(rateEditForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(rateEditForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(rateEditForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            editPropClassUse: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: propClassForm.attr('data-action'),
                    data: propClassForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (propClassForm.valid()) {
                            Common.showSpinner(propClassForm, "Saving Property Class Use Information...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(propClassForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(propClassForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(propClassForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            editTemplatePath: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: templateForm.attr('data-action'),
                    data: templateForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (templateForm.valid()) {
                            Common.showSpinner(templateForm, "Saving Template Path..");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(templateForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(templateForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(templateForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            editPropClassUseStatus: function () {
                var ajaxOptions = {
                    type: 'POST',
                    url: statusForm.attr('data-action'),
                    data: statusForm.serialize(),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        if (statusForm.valid()) {
                            Common.showSpinner(statusForm, "Saving Property Class Use Status Information...");
                            return true;
                        } else {
                            return false;
                        }

                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(statusForm);
                    if (response.status === "ok" && response.code === "00") {
                        Common.showMessage(response.message);
                        IRate.loadRates();
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(statusForm);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(statusForm);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            loadRates: function () {
                var pgContent=$("#pg-content");
                var ajaxOptions = {
                    type: 'GET',
                    url: treeDiv.attr('data-tree-url'),
                    dataType: "json",
                    beforeSend: function (xhr) {
                        Common.showSpinner(pgContent, "Loading Revenue Items...");
                        return true;
                    }
                };
                $.ajax(ajaxOptions).done(function (response) {
                    Common.hideSpinner(pgContent);
                    if (response.status === "ok" && response.code === "00") {
                        var data = IRate.toJstreeObject(response.data);
                        console.log(data);
                        try {
                            Common.showSpinner(treeDiv, "Loading Revenue Items...");
                            treeDiv.jstree(true).settings.core.data = data;
                            treeDiv.jstree(true).refresh();
                            Common.hideSpinner(pgContent);
                        } catch (e) {
                            Common.hideSpinner(pgContent);
                            console.log(e);
                        }
                    } else {
                        Common.onError(response.message);
                    }
                }).always(function (xhr) {
                    Common.hideSpinner(treeDiv);
                }).fail(function (xhr, textStatus, error) {
                    Common.hideSpinner(pgContent);
                    if (textStatus !== "canceled") {
                        Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                    }
                });

            },
            getRate: function (el) {
                try {
                    if (parseFloat(el.minamount) > 0) {
                        return el.minamount;
                    } else if (parseFloat(el.flatrate) > 0) {
                        return el.flatrate;
                    }
                    return 0;
                } catch (e) {
                    return 0;
                }
            },
            toJstreeObject: function (items) {
                return items.map(function (el) {
                    var o = Object.assign({}, el);
                    var rate = IRate.getRate(el);
                    o.text = el.name + ((rate > 0) ? ' (' + accounting.formatMoney(rate, '') + ')' : '');

                    delete o.parent;
                    o.state = {'opened': false};
                    // o.parent = (el.parent > 0) ? el.parent.toString() : "#";
                    //  o.id = el.id.toString();
                    if (o.children && o.children.length > 0) {
                        o.children = IRate.toJstreeObject(o.children);
                    }
                    return o;
                });
            },
            validateRateForm: function () {
                Common.validateForm(rateForm, {
                    name: "required",
                    code: "required",
                    startGeneration: "required",
                    rateImpost: {
                        required: function () {
                            return ($("#isRated").val() === 'yes');
                        },
                        number: true,
                        min: function () {
                            return ($("#isRated").val() === 'yes') ? 1 : 0;
                        }
                    },
                    flatRate: {
                        required: function () {
                            return ($("#isRated").val() === 'no');
                        },
                        number: true,
                        min: function () {
                            return ($("#isRated").val() === 'no') ? 1 : 0;
                        }
                    },
                    minAmount: {
                        required: true,
                        number: true
                    },
                    maxAmount: {
                        required: true,
                        number: true
                    },
                    isRated: 'required',
                    isApplicable: 'required'
                }, {});
            },
            validatePCUForm: function () {
                Common.validateForm(pcuForm, {
                    name: "required",
                    code: "required"

                }, {});
            },
            validateRateUpdateForm: function () {
                Common.validateForm(rateEditForm, {
                    isRated: 'required',
                    rateImpost: {
                        required: function () {
                            return ($(".isRated").val() === 'yes');
                        },
                        number: true,
                        min: function () {
                            return ($(".isRated").val() === 'yes') ? 1 : 0;
                        }
                    },
                    flatRate: {
                        required: function () {
                            return ($(".isRated").val() === 'no');
                        },
                        number: true,
                        min: function () {
                            return ($(".isRated").val() === 'no') ? 1 : 0;
                        }
                    },
                    minAmount: {
                        required: true,
                        number: true
                    },
                    maxAmount: {
                        required: true,
                        number: true
                    }

                    // frequency: 'required'
                }, {});
            },
            validatePropClassForm: function () {
                Common.validateForm(propClassForm, {
                    name: "required",
                    code: "required",
                    startGeneration: "required"
                }, {});
            },
            validateTemplateForm: function () {
                Common.validateForm(templateForm, {
                    tmpPath: "required"
                }, {});
            },
            validateStatusForm: function () {
                Common.validateForm(statusForm, {
                    isApplicable: "required"
                }, {});
            },
        };
    }();

$(function () {
    IRate.init();
});
