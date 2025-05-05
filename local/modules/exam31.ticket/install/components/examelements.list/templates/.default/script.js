BX.ready(function() {
    BX.SidePanel.Instance.bindAnchors({
        rules: [
            {
                condition: ['/exam31/detail/'],
                options: {
                    width: 1500,
                    label: {
                        text: "Элемент",
                    },
                }
            }
        ]
    });
});