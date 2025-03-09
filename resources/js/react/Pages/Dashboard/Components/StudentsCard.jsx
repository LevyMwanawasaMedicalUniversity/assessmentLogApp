import React, { useState, useEffect } from 'react';
import { Card, Col } from 'react-bootstrap';
import axios from 'axios';

export default function StudentsCard() {
    const [studentCount, setStudentCount] = useState(0);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/students-with-ca');
                setStudentCount(response.data.count);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching student data:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <Col xxl={4} md={6} className="mb-4">
            <Card className="info-card sales-card h-100 shadow-sm">
                <Card.Body>
                    <h5 className="card-title">Students With CA <span>| Uploaded</span></h5>

                    <div className="d-flex align-items-center">
                        <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i className="bi bi-people"></i>
                        </div>
                        <div className="ps-3">
                            {loading ? (
                                <div className="spinner-border spinner-border-sm text-primary" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            ) : (
                                <>
                                    <h6>{studentCount}</h6>
                                    <span className="text-success small pt-1 fw-bold">From</span> 
                                    <span className="text-muted small pt-2 ps-1">Assessments System</span>
                                </>
                            )}
                        </div>
                    </div>
                </Card.Body>
            </Card>
        </Col>
    );
}
