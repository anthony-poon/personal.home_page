import React from "react"

export default class GalleryNav extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className={"gallery-nav"}>
                <div className={"container"}>
                    <div className={"row py-2"}>
                        <div className={"col-auto d-flex align-items-center"}>
                            <span className={"btn-like text-secondary p-2"} onClick={() => {
                                this.props.onCloseBtnClick()
                            }}>
                                <i className="fas fa-times"/>
                            </span>
                        </div>
                        <div className={"col d-flex align-items-center"}>
                            <span className={"text-secondary"}>
                                {this.props.header}
                            </span>
                        </div>
                        <div className={"col-auto d-flex align-items-center"}>
                            {this.props.actionBar}
                        </div>
                    </div>
                    <div className={"gallery-nav__progress-bar"} style={{
                        width: this.props.progress + "%"
                    }}/>
                </div>
            </div>
        )
    }
}

GalleryNav.defaultProps = {
    process: 0
};