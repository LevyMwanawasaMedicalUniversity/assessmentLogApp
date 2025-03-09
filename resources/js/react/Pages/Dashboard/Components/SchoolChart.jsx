import React, { useState, useEffect } from 'react';
import { Card, Button } from 'react-bootstrap';
import axios from 'axios';

export default function SchoolChart() {
    const [chartData, setChartData] = useState({
        schoolNames: [],
        totalCourses: [],
        coursesWithCA: []
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/school-chart');
                setChartData({
                    schoolNames: response.data.schoolNames,
                    totalCourses: response.data.totalCourses,
                    coursesWithCA: response.data.coursesWithCA
                });
                setLoading(false);
                
                // Initialize ECharts after data is loaded
                if (response.data.schoolNames.length > 0) {
                    initChart(
                        response.data.schoolNames,
                        response.data.totalCourses,
                        response.data.coursesWithCA
                    );
                }
            } catch (error) {
                console.error('Error fetching school chart data:', error);
                setLoading(false);
            }
        };

        fetchData();
        
        // Cleanup function to handle component unmounting
        return () => {
            const chartDom = document.getElementById('verticalBarChart');
            if (chartDom && window.echarts) {
                const chart = window.echarts.getInstanceByDom(chartDom);
                if (chart) {
                    chart.dispose();
                }
            }
        };
    }, []);

    // Initialize the ECharts instance
    const initChart = (schoolNames, totalCourses, coursesWithCA) => {
        const chartDom = document.getElementById('verticalBarChart');
        if (!chartDom || !window.echarts) return;
        
        const chart = window.echarts.init(chartDom);
        
        const option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['Total Courses', 'Courses with CA']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value',
                boundaryGap: [0, 0.01]
            },
            yAxis: {
                type: 'category',
                data: schoolNames
            },
            series: [
                {
                    name: 'Total Courses',
                    type: 'bar',
                    data: totalCourses,
                    itemStyle: {
                        color: '#4154f1'
                    }
                },
                {
                    name: 'Courses with CA',
                    type: 'bar',
                    data: coursesWithCA,
                    itemStyle: {
                        color: '#2eca6a'
                    }
                }
            ]
        };
        
        chart.setOption(option);
        
        // Handle window resize
        window.addEventListener('resize', () => {
            chart.resize();
        });
    };

    const exportCSV = () => {
        const { schoolNames, totalCourses, coursesWithCA } = chartData;
        
        let csvContent = "School,Total Courses,Courses with CA\n";
        
        for (let i = 0; i < schoolNames.length; i++) {
            csvContent += `${schoolNames[i]},${totalCourses[i]},${coursesWithCA[i]}\n`;
        }
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'ca_per_school.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    return (
        <Card className="shadow-sm mb-4">
            <Card.Body>
                <div className="d-flex justify-content-between align-items-center mb-3">
                    <h5 className="card-title mb-0">CA PER SCHOOL</h5>
                    <Button 
                        variant="primary" 
                        size="sm" 
                        onClick={exportCSV}
                        disabled={loading}
                    >
                        <i className="bi bi-download me-1"></i> Export CSV
                    </Button>
                </div>
                
                {loading ? (
                    <div className="d-flex justify-content-center align-items-center" style={{ height: '400px' }}>
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                    <div id="verticalBarChart" style={{ minHeight: '400px' }} className="echart mt-3"></div>
                )}
            </Card.Body>
        </Card>
    );
}
