import React from "react";
import "intersection-observer";
import _ from "underscore";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons"
import { faGithub, faLinkedin } from "@fortawesome/free-brands-svg-icons"
export default class IndexApp extends React.Component{
    sessionBreaks = [];
    state = {
        sessionIndex: -1
    };

    componentDidMount() {
        let scrollY = this.sessionBreaks.map((val, index) => {
            return val.getBoundingClientRect().top + document.documentElement.scrollTop;
        });
        window.addEventListener("scroll", () => {
            const offsetY = document.documentElement.clientHeight * 0.5;
            const windowY = document.documentElement.scrollTop + document.documentElement.clientHeight - offsetY;

            const index = _.findLastIndex(scrollY, (y) => {
                return y <= windowY
            });
            this.setState({
                sessionIndex: index
            })
        })
    }

    registerBreaks = (el) => {
        this.sessionBreaks.push(el);
    };


    render() {
        return (
            <div className={"index-page__wrapper row no-gutters my-5"}>
                <div className={"index-page__bg-float"}>
                    <div className={"text-right"}>Anthony</div>
                    <div className={"text-right"}>Poon</div>
                </div>
                <div className={"index-page__lhs ml-auto col-12 col-md-8 col-lg-7"}>
                    <div className={"px-lg-5 px-3"}>
                        <div className={"index-page__hero mb-5 text-primary"}>
                            <div className={"hero__name"}>
                                <div>Anthony</div>
                                <div>Poon</div>
                            </div>
                            <div className={"hero__statement-1 text-info"}>
                                Programmer and Sysadmin
                            </div>
                            <div className={"hero__statement-2 text-info"}>
                                PHP, Javascript and React
                            </div>
                        </div>
                        <div className={"index-page__sub py-5"} ref={this.registerBreaks.bind(this)} id={"about"}>
                            <h4 className={"mb-4"}>About Me</h4>
                            <p>I am a <span className={"text-info"}>Sysadmin</span> working in Hong Kong. I also do <span className={"text-info"}>Full Stack Development</span> in work and on the side.</p>
                            <p>For coding I work with PHP, Node.js, React. I am also experienced with Linux, Apache, Nginx and network administration.</p>
                        </div>
                        <div className={"index-page__sub py-5"} ref={this.registerBreaks.bind(this)} id={"work"}>
                            <h4 className={"mb-4"}>Recent Work</h4>
                            <div className={"py-4"}>
                                <h5 className={"text-info"}>React Gallery</h5>
                                <p>
                                    My most recent work is a gallery app developed with <span className={"text-info"}>React</span>.  It features <span className={"text-info"}>lazy loading</span>, <span className={"text-info"}>transition animation</span> and <span className={"text-info"}>responsive design.</span>
                                </p>
                                <div>
                                    <a href={"/gallery"} className={"btn btn-link btn-block text-left"}>
                                        <FontAwesomeIcon icon={faArrowLeft}/>
                                        <span className={"ml-3"}>Show me</span>
                                    </a>
                                </div>
                                <div>
                                    <a href={"https://github.com/anthony-poon/personal.home_page/tree/master/assets/gallery"} className={"btn btn-link btn-block text-left"}>
                                        <FontAwesomeIcon icon={faArrowLeft}/>
                                        <span className={"ml-3"}>Source Code</span>
                                    </a>
                                </div>
                            </div>
                            <div className={"py-4"}>
                                <h5 className={"text-info"}>HRIS</h5>
                                <p>HRIS is a in-house developed web application used internally. It is developed with <span className={"text-info"}>PHP, Symfony Framework, Javascript, React</span></p>
                                <div>
                                    Internal staffs use this system to:
                                    <ul>
                                        <li>Apply for leaves</li>
                                        <li>Apply for business travel</li>
                                        <li>View absentee</li>
                                        <li>etc...</li>
                                    </ul>
                                </div>
                                <div>
                                    <a className={"btn btn-link btn-block text-left disabled"}>
                                        <FontAwesomeIcon icon={faArrowLeft}/>
                                        <span className={"ml-3"}>Show me (Available Soon)</span>
                                    </a>
                                </div>
                                <div>
                                    <a className={"btn btn-link btn-block text-left disabled"}>
                                        <FontAwesomeIcon icon={faArrowLeft}/>
                                        <span className={"ml-3"}>Source Code (Available Soon)</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div className={"index-page__sub py-5"} ref={this.registerBreaks.bind(this)} id={"edu"}>
                            <h4 className={"mb-4"}>My Education</h4>
                            <div className="mb-3">
                                <div className={"font-weight-bold text-info"}>
                                    The Chinese University of Hong Kong
                                </div>
                                <div>
                                    Master of Science in Computer Science
                                </div>
                                <div className={"text-secondary"}>
                                    2016 - 2018
                                </div>
                            </div>
                            <div className="mb-3">
                                <div className={"font-weight-bold text-info"}>
                                    HKU Space
                                </div>
                                <div>
                                    Postgraduate Diploma in Information Technology
                                </div>
                                <div className={"text-secondary"}>
                                    2014 - 2015
                                </div>
                            </div>
                            <div className="mb-3">
                                <div className={"font-weight-bold text-info"}>
                                    University of Western Ontario
                                </div>
                                <div>
                                    Bachelor of Management and Organizational Studies
                                </div>
                                <div className={"text-secondary"}>
                                    2009 - 2011
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className={"col-3 mr-auto d-none d-md-block"}>
                    <div className={"index-page__rhs"}>
                        <div className={"index-rhs__top"}>
                            <a href={"/#about"} className={this.state.sessionIndex === 0 ? "text-primary" : ""}>About Me</a>
                            <a href={"/#work"} className={this.state.sessionIndex === 1 ? "text-primary" : ""}>Recent Work</a>
                            <a href={"/#edu"} className={this.state.sessionIndex === 2 ? "text-primary" : ""}>My Education</a>
                        </div>
                        <div className={"index-rhs__bottom"}>
                            <a href={"https://github.com/anthony-poon"} className={"brand-icon"}>
                                <FontAwesomeIcon icon={faGithub}/>
                            </a>
                            <a href={"https://www.linkedin.com/in/anthony-poon-b0315a177"} className={"brand-icon"}>
                                <FontAwesomeIcon icon={faLinkedin}/>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        );
    }

}