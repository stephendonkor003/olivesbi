/**
 * Created by degre on 4/30/2021.
 */
var Dashboard = function () {
    var dbPage = $("#db-row");
    return {
        init: function () {
            Dashboard.loadDashboardData();
            try {
                Dashboard.initBarChart('dataCollection', 'chart');
                Dashboard.initPieChart('dataCollection','prr','dcprrChart');
                Dashboard.initPieChart('dataCollection','bus','dcbusChart');

                Dashboard.initBarChart('distribution', 'distChart');
                Dashboard.initPieChart('distribution','prr','distprrChart');
                Dashboard.initPieChart('distribution','bus','distbusChart');


                Dashboard.initBarChart('payment', 'pmtChart');
                Dashboard.initPieChart('payment','prr','pmtprrChart');
                Dashboard.initPieChart('payment','bus','pmtbusChart');
            } catch (e) {
                console.log(e);
            }
            $(document).on('click', '.dt-view', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                FNC.scrollToBottom();
                var type = $(this).attr('data-type');
                var period = $(this).attr('data-period');
                FNC.setTitle(type, period);
                FNC.reloadDetailsDT(type, period);

            });
        },
        setTotals: function (data) {
            $(".lbl-register-count").text(data.regCount);
            $(".lbl-distribution-count").text(data.dstCount);
            $(".lbl-pay-count").text(data.payCount);
            $(".lbl-pay-total").text(accounting.formatMoney(data.paySum, ''));
        },
        setDataCounts: function (data) {
            try {
                $(".dc-lbl-bus").text(data.dataCollection.counts.busCount);
                $(".dc-lbl-prr").text(data.dataCollection.counts.prrCount);

                $(".dist-lbl-bus").text(data.distribution.counts.busCount);
                $(".dist-lbl-prr").text(data.distribution.counts.prrCount);

                $(".pmt-lbl-bus").text(data.payment.counts.busCount);
                $(".pmt-lbl-prr").text(data.payment.counts.prrCount);
            }catch(e){}
        },
        scrollToBottom: function () {
            $("html, body").animate({scrollTop: $(document).height()}, "slow");
            return false;
        },
        loadDashboardData: function () {
            var requestUrl = dbPage.attr('data-db-url');
            var ajaxOptions = {
                type: "GET",
                url: requestUrl,
                dataType: 'json',
                beforeSend: function (formData, jqForm, options) {
                    Common.showSpinner(dbPage, "Loading Data...");
                    return true;
                }
            };
            $.ajax(ajaxOptions).done(function (response) {
                Common.hideSpinner(dbPage);
                if (response.status === "ok") {
                    Dashboard.setTotals(response.data.summaryCounts);
                    Dashboard.setDataCounts(response.data);
                } else {
                    Common.onError(response.msg);
                }
            }).always(function (xhr) {
                Common.hideSpinner(dbPage);
            }).fail(function (xhr, textStatus, error) {
                Common.hideSpinner(dbPage);
                if (textStatus !== "canceled") {
                    Common.onError("Sorry An Error Occured Whiles Processing The Request. Kindly Try Again Later.");
                }
            });
        },
        getTitle:function (dataType) {
            switch (dataType){
                case 'dataCollection':
                    return 'Data Collection';
                case 'distribution':
                    return 'Distribution';
                case 'payment':
                    return 'Payment';
            }
        },
        initBarChart: function (dataType, chartDivId) {
            const chart = new Chartisan({
                el: '#' + chartDivId,
                url: dbPage.attr('data-chart-url'),
                options: {
                    headers: {
                        'x-data-type': dataType,
                        'x-data-period': dbPage.attr('data-period')
                    }
                },
                hooks: new ChartisanHooks()
                    .legend({
                        bottom: 15,
                        left: 'center'
                    })
                    .colors()
                    .datasets(['bar', 'bar'])
                    .tooltip({
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross'
                        }
                    })
                    .title({
                        text: 'Assembly '+Dashboard.getTitle(dataType)+' Statistics', subtext: Dashboard.getTitle(dataType)+' Summary',
                        left: 'center'
                    })

            });
        },
        initPieChart: function (dataType, entityType, chartDivId) {
            // entityType=prr or bus
            var title=(entityType=='prr')?'Property':'Business';

            const chart = new Chartisan({
                el: '#' + chartDivId,
                url: dbPage.attr('data-pie-chart-url'),
                options: {
                    headers: {
                        'x-data-type': dataType,
                        'x-entity-type': entityType,
                        'x-data-period': dbPage.attr('data-period')
                    }
                },
                hooks: new ChartisanHooks()
                    .legend({
                        bottom: 15,
                        left: 'center'
                    })
                    .colors()
                    .axis(false)
                    .datasets([{ type: 'pie', radius: ['50%'] }])
                    .tooltip()
                    .title({
                        text: title+' '+Dashboard.getTitle(dataType),
                        left: 'center'
                    })

            });
        },
    };
}();

$(function () {
    Dashboard.init();
});