"use strict"; 
    var KTAccountSecuritySummary=function(){
        var t=function(t,e,a,r,z,s){
            var i=document.querySelector(e),n=parseInt(KTUtil.css(i,"height"));if(i){
                var o={ 
                    series:[
                        {name:"STMIK",data:a},
                        {name:"STIKES",data:r},
                        {name:"STIE",data:z}
                    ],
                        chart:{
                            fontFamily:"inherit",
                            type:"bar",
                            height:n,
                            toolbar:{show:!2}},
                            plotOptions:{
                                bar:{
                                    horizontal:!2,
                                    columnWidth:["35%"],
                                    endingShape:"rounded"}
                                },
                                legend:{show:!2},
                                dataLabels:{enabled:!2},
                                stroke:{
                                    show:!0,
                                    width:2,
                                    colors:["transparent"]
                                },
                                    xaxis:{categories:["Jul 22","Aug 22","Sep 22","Oct 22","Nov 22","Dec 22","Jan 23","Feb 23","Mar 23","Apr 23","May 23","Jun 23"],
                                    axisBorder:{show:!2},
                                    axisTicks:{show:!2},
                                    labels:{
                                        style:{
                                            colors:KTUtil.getCssVariableValue("--bs-gray-400"),
                                            fontSize:"12px"}
                                        }},
                                            yaxis:{
                                                labels:{
                                                    style:{
                                                        colors:KTUtil.getCssVariableValue("--bs-gray-400"),
                                                        fontSize:"12px"
                                                    }}
                                                    },
                                        fill:{opacity:1},
                                        states:{
                                            normal:{
                                                filter:{type:"none",value:0}},
                                                hover:{filter:{type:"none",value:0}},
                                                active:{
                                                    allowMultipleDataPointsSelection:!2,
                                                    filter:{type:"none",value:0}
                                                }},
                                        tooltip:{
                                            style:{fontSize:"12px"},
                                            y:{formatter:function(t){return t+" Orang"}}
                                        },
                                            colors:[
                                                KTUtil.getCssVariableValue("--bs-primary"),
                                                KTUtil.getCssVariableValue("--bs-success"),
                                                KTUtil.getCssVariableValue("--bs-danger") 
                                            ],
                                            grid:{
                                                borderColor:KTUtil.getCssVariableValue("--bs-gray-200"), 
                                                strokeDashArray:4,
                                            yaxis:{
                                                lines:{show:!0}}
                                            }},
                                                u=new ApexCharts(i,o),
                                                l=!2,_=document.querySelector(t);!0===s&&(u.render(),l=!0),
                                                _.addEventListener("shown.bs.tab",(function(t){0==l&&(u.render(),l=!0)}))}};return{init:function(){
                                                    t("#kt_security_summary_tab_hours_agents","#kt_security_summary_chart_hours_agents",[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!0),t("#kt_security_summary_tab_hours_clients","#kt_security_summary_chart_hours_clients",[50,70,90,117,80,65,80,90,115,95,70,84],[50,70,90,117,80,65,80,90,115,95,70,84],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!2),t("#kt_security_summary_tab_day","#kt_security_summary_chart_day_agents",[50,70,80,100,90,65,80,90,115,95,70,84],[50,70,90,117,60,65,80,90,100,95,70,84],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!2),t("#kt_security_summary_tab_day_clients","#kt_security_summary_chart_day_clients",[50,70,100,90,80,65,80,90,115,95,70,84],[50,70,90,115,80,65,80,90,115,95,70,84],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!2),t("#kt_security_summary_tab_week","#kt_security_summary_chart_week_agents",[50,70,75,117,80,65,80,90,115,95,50,84],[50,60,90,117,80,65,80,90,115,95,70,84],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!2),t("#kt_security_summary_tab_week_clients","#kt_security_summary_chart_week_clients",[50,70,90,117,80,65,80,90,100,80,70,84],[50,70,90,117,80,65,80,90,100,95,70,84],[Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1),Math.floor((Math.random() * 120) + 1)],!2)}}}();
                                                    KTUtil.onDOMContentLoaded((
                                                        function(){ KTAccountSecuritySummary.init() }));