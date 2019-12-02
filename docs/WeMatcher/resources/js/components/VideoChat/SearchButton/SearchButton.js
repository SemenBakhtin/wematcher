import React from 'react'

const SearchButton = props => {
  return <button className="common-btn primary shadow" onClick={props.action}>{props.t("Start searching")}</button>
}

export default SearchButton
