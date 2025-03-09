import React, { useState, useEffect } from 'react';
import { Card } from 'react-bootstrap';
import axios from 'axios';

export default function RecentActivity() {
    const [activities, setActivities] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('/api/dashboard/recent-activities');
                setActivities(response.data.activities);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching recent activities:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    // Function to determine the appropriate badge color based on event type
    const getBadgeClass = (event) => {
        switch(event) {
            case 'created':
                return 'text-success';
            case 'updated':
                return 'text-info';
            case 'deleted':
                return 'text-danger';
            default:
                return 'text-warning';
        }
    };

    return (
        <Card className="shadow-sm mb-4">
            <Card.Body>
                <h5 className="card-title">Recent Activity <span>| Today</span></h5>

                <div className="activity">
                    {loading ? (
                        <div className="d-flex justify-content-center py-4">
                            <div className="spinner-border text-primary" role="status">
                                <span className="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    ) : activities.length > 0 ? (
                        activities.map((activity, index) => (
                            <div className="activity-item d-flex" key={index}>
                                <div className="activite-label">{activity.time_ago}</div>
                                <i className={`bi bi-circle-fill activity-badge ${getBadgeClass(activity.event)} align-self-start`}></i>
                                <div className="activity-content">
                                    {activity.user_name} {activity.event} a {activity.auditable_type}
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="text-center py-3">
                            <p className="text-muted">No recent activities found</p>
                        </div>
                    )}
                </div>
            </Card.Body>
        </Card>
    );
}
