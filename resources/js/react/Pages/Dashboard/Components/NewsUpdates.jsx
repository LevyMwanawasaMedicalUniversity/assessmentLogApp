import React, { useState, useEffect } from 'react';
import { Card } from 'react-bootstrap';
import axios from 'axios';

export default function NewsUpdates() {
    const [announcements, setAnnouncements] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/announcements');
                setAnnouncements(response.data.announcements);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching announcements:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <Card className="shadow-sm mb-4">
            <Card.Body>
                <h5 className="card-title">News & Updates</h5>

                <div className="news">
                    {loading ? (
                        <div className="d-flex justify-content-center py-4">
                            <div className="spinner-border text-primary" role="status">
                                <span className="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    ) : announcements.length > 0 ? (
                        announcements.map((announcement, index) => (
                            <div className="post-item clearfix" key={index}>
                                <div className="d-flex align-items-center mb-2">
                                    <i className="bi bi-megaphone me-2 text-primary fs-5"></i>
                                    <h4 className="mb-0">{announcement.title}</h4>
                                </div>
                                <p>{announcement.content}</p>
                                <p className="text-muted small">{announcement.date}</p>
                            </div>
                        ))
                    ) : (
                        <div className="text-center py-3">
                            <p className="text-muted">No announcements available</p>
                        </div>
                    )}
                </div>
            </Card.Body>
        </Card>
    );
}
