import React, { useState, useEffect } from 'react';
import { Card, Col } from 'react-bootstrap';
import axios from 'axios';

export default function CoursesFromEduroleCard() {
    const [courseCount, setCourseCount] = useState(0);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/courses-from-edurole');
                setCourseCount(response.data.count);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching courses data:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <Col xxl={4} md={6} className="mb-4">
            <Card className="info-card revenue-card h-100 shadow-sm">
                <Card.Body>
                    <h5 className="card-title">Courses from Edurole <span>| Total</span></h5>

                    <div className="d-flex align-items-center">
                        <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i className="bi bi-calendar2-week-fill"></i>
                        </div>
                        <div className="ps-3">
                            {loading ? (
                                <div className="spinner-border spinner-border-sm text-primary" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            ) : (
                                <>
                                    <h6>{courseCount}</h6>
                                    <span className="text-primary small pt-1 fw-bold">With Coordinators Assigned</span>
                                </>
                            )}
                        </div>
                    </div>
                </Card.Body>
            </Card>
        </Col>
    );
}
