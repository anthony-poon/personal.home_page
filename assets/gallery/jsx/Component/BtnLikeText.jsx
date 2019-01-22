import React from "react";
import loadingBar from "../../image/loading_bar.svg";

export default class BtnLikeText extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            isDisabled: this.props.isDisabled,
            isLoading: false
        }
    }
    // TODO: componentWillUnmount() to fix memory leak?
    render() {
        let cssClass = [
            "btn-like"
        ];
        if (this.props.isDisabled) {
            cssClass.push("disabled");
            cssClass.push("text-secondary");
        } else {
            cssClass.push(this.props.colorClass)
        }
        cssClass = cssClass.join(" ");
        return (
            <span className={cssClass} onClick={async () => {
                if (!this.state.isDisabled) {
                    this.setState({
                        isLoading: true
                    });
                    let rtn = await this.props.onClick();
                    this.setState({
                        isLoading: false
                    });
                }
            }}>
                <span className={this.state.isLoading ? "d-none" : "d-inline-block"}>
                    {this.props.content}
                </span>
                <img className={this.state.isLoading ? "d-inline-block visible" : "d-none invisible"} style={{height: "1.5rem"}} src={loadingBar}/>
            </span>
        )
    }
}

BtnLikeText.defaultProps = {
    colorClass: "text-primary",
    isDisabled: false
};