import React, { useState, useEffect } from 'react';
import { Card, Col } from 'react-bootstrap';
import axios from 'axios';

export default function CoursesFromLMMAXCard() {
    const [courseCount, setCourseCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const [hasPermission, setHasPermission] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/courses-from-lmmax');
                setCourseCount(response.data.count);
                setHasPermission(response.data.hasRegistrarPermission);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching LMMAX courses data:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <Col xxl={4} xl={12} className="mb-4">
            <Card className="info-card customers-card h-100 shadow-sm">
                <Card.Body>
                    <h5 className="card-title">Courses From LM-MAX <span>| Total</span></h5>
                    
                    {hasPermission ? (
                        <a href="/coordinator/programmes-with-ca" className="text-decoration-none">
                            <CardContent loading={loading} courseCount={courseCount} />
                        </a>
                    ) : (
                        <CardContent loading={loading} courseCount={courseCount} />
                    )}
                </Card.Body>
            </Card>
        </Col>
    );
}

// Extracted component to avoid code duplication
function CardContent({ loading, courseCount }) {
    return (
        <div className="d-flex align-items-center">
            <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i className="bi bi-receipt"></i>
            </div>
            <div className="ps-3">
                {loading ? (
                    <div className="spinner-border spinner-border-sm text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                ) : (
                    <>
                        <h6>{courseCount}</h6>
                        <span className="text-success small pt-1 fw-bold">With Continuous Assessments</span>
                    </>
                )}
            </div>
        </div>
    );
}
