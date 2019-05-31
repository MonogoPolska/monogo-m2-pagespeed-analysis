#### Magento 2 module for Google PageSpeed Analysis.

This module will work only with Magento 2.3.0 and higher

Module is using chart.js : https://www.chartjs.org/

# **Install**

### Git
- Locate the **/app/code** directory which should be under the magento root installation.
- If the **code** folder is not there, create it.
- Create a folder **Monogo** inside the **code** folder. 
- Change to the **Monogo** folder and clone the Git repository (https://github.com/MonogoPolska/monogo-m2-pagespeed-analysis.git) into **Monogo** specifying the local repository folder to be **OptimizeDatabase** 
e.g. 

``` git clone https://github.com/MonogoPolska/monogo-m2-pagespeed-analysis PagespeedAnalysis```

### Composer
```composer require monogo/pagespeed-analysis```

### Magento Setup
- Run Magento commands

```php bin/magento setup:upgrade```

```php bin/magento setup:di:compile```

```php bin/magento setup:static-content:deploy```

# **App Configuration Options**

Go to Stores->Configuration->Monogo->Pagespeed Analysis

- Enable module **Default value is 1 (Yes)**
- Provide PageSpeed API Key. You can get one from https://developers.google.com/speed/docs/insights/v5/get-started -> Get A Key 
- Select strategy [Mobile,Desktop] 
- Use Magento Cron - You can disable Magento cron and run PageSpeed scan from shell (recommended) **Default value is 0 (No)**
- Cron schedule - Use Crontab Format (Eg. "0 0 * * *" every day at 01:05)
- Provide website / websites to scan
- Additional charts configuration (show last X days, colors, chart height, use auto scale)
- Debug settings

### Default values:
enable: 0

strategy: mobile,desktop   
            
use_cron: 0

height: 150 px

history: 30 days

**Chart colors**

performance: #4268b3

seo: #d46fd4

pwa: #f2970e

best_practices: #24a318

accessibility: #f5d60c

ttfb: #0c90f5


# **Shell**

```
Usage:  php bin/magento monogo:pagespeed:run
```

You can setup external cron to run:

```* */4 * * * php bin/magento monogo:pagespeed:run```

# **Report**

Go to Reports->Statistics->Pagespeed

In tabs you will have charts with desktop/mobile view. Under Show details button you will see last scan details.

In Grid you can add comment to specific record (for example: **A/B testing start** or **Production deployment**) 
This comment will be visible on charts after page refresh.

# **TODO**
- email weekly reports
- Tests