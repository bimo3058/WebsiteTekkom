@once
<style>
            .mp-assessment-field {
                position: relative !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
            }

            .mp-assessment-trigger {
                box-sizing: border-box !important;
                width: 100% !important;
                height: 46px !important;
                padding: 0 14px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                border: 1px solid #DFE1E7 !important;
                border-radius: 9px !important;
                background: #FFFFFF !important;
                color: #0D0D12 !important;
                font-size: 13px !important;
                line-height: 18px !important;
                text-align: left !important;
                cursor: pointer !important;
            }

            .mp-assessment-trigger.is-open {
                border-color: #0B266E !important;
                background: #F4F6FC !important;
                box-shadow: 0 0 0 1px #0B266E !important;
            }

            .mp-assessment-trigger > span {
                display: block !important;
                min-width: 0 !important;
                font-size: 13px !important;
                line-height: 18px !important;
            }

            .mp-assessment-trigger > svg {
                display: block !important;
                flex: 0 0 16px !important;
                margin-left: auto !important;
                transform: rotate(0deg) !important;
                transition: transform 0.2s ease !important;
            }

            .mp-assessment-trigger.is-open > svg {
                transform: rotate(180deg) !important;
            }

            .mp-assessment-menu {
                position: absolute !important;
                z-index: 30 !important;
                top: calc(100% + 6px) !important;
                left: 0 !important;
                right: 0 !important;
                padding: 6px !important;
                background: #FFFFFF !important;
                border: 1px solid #DFE1E7 !important;
                border-radius: 9px !important;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.14) !important;
            }

            .mp-assessment-option {
                box-sizing: border-box !important;
                width: 100% !important;
                min-height: 38px !important;
                padding: 10px 12px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                border-radius: 7px !important;
                background: #FFFFFF !important;
                color: #353849 !important;
                font-size: 13px !important;
                line-height: 18px !important;
                text-align: left !important;
                cursor: pointer !important;
            }

            .mp-assessment-option.is-selected {
                background: #EEF1FA !important;
                color: #0B266E !important;
                font-weight: 600 !important;
            }

            .mp-assessment-option > span {
                display: block !important;
                font-size: 13px !important;
                line-height: 18px !important;
            }

            .mp-assessment-option > svg {
                display: block !important;
                flex: 0 0 15px !important;
                margin-left: auto !important;
            }

            .mp-assessment-add {
                box-sizing: border-box !important;
                width: 100% !important;
                height: 48px !important;
                margin-top: 10px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
                border: 1px dashed #D8DCE5 !important;
                border-radius: 9px !important;
                background: #FCFCFD !important;
                color: #0B266E !important;
                font-size: 13px !important;
                line-height: 18px !important;
                font-weight: 600 !important;
            }

            .mp-assessment-add > span:first-child {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 21px !important;
                line-height: 1 !important;
                font-weight: 400 !important;
            }

</style>
@endonce
