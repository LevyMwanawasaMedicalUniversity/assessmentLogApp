import React, { useState, useEffect } from 'react';
import { Card, Col } from 'react-bootstrap';
import ReactApexChart from 'react-apexcharts';
import axios from 'axios';

export default function ProgrammeChart() {
    const [chartData, setChartData] = useState({
        programmeCodes: [],
        coursesWithCA: [],
        coursesFromEdurole: []
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/programme-chart');
                setChartData({
                    programmeCodes: response.data.programmeCodes,
                    coursesWithCA: response.data.coursesWithCA,
                    coursesFromEdurole: response.data.coursesFromEdurole
                });
                setLoading(false);
            } catch (error) {
                console.error('Error fetching programme chart data:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    const chartOptions = {
        chart: {
            type: 'bar',
            height: 350,
            fontFamily: "'Open Sans', sans-serif",
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                borderRadius: 4
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: chartData.programmeCodes,
        },
        yaxis: {
            title: {
                text: 'Number of Courses'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " courses"
                }
            }
        },
        theme: {
            mode: 'light',
            palette: 'palette1'
        }
    };

    const series = [
        {
            name: 'Courses with CA',
            data: chartData.coursesWithCA
        }, 
        {
            name: 'Courses from Edurole',
            data: chartData.coursesFromEdurole
        }
    ];

    return (
        <Card className="shadow-sm mb-4">
            <Card.Body>
                <h5 className="card-title">Course With CA Per Programme</h5>
                
                {loading ? (
                    <div className="d-flex justify-content-center align-items-center" style={{ height: '350px' }}>
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                    <div id="columnChart" className="mt-3">
                        <ReactApexChart 
                            options={chartOptions} 
                            series={series} 
                            type="bar" 
                            height={350} 
                        />
                    </div>
                )}
            </Card.Body>
        </Card>
    );
}
