# cartPrice
Backend technical test


**Goal**

Provide an endpoint that will consume the openexchangerates.org (feel free to use also any other solution) to calculate the cart value at current exchange rates, rounded to 2 decimal places. Must be written in PHP.  The source code of the project should include automated tests.


**to build and run docker containers**

ater

git clone https://github.com/dmkopylets/cartPrice.git


copy file .env to .env.local

make dc-build

**to start the containers use:**

make dc-up

**then install packages for vendors:**

make composer-i

***

**it will be possible to test the application at localhost**

http://localhost/process-payload - now you can send your payload here
    ![requestSending.png](docs%2FrequestSending.png)  
 

and to stop docker containers use 

**make dc-down**



**the level of test coverage can be estimated here**

[tests coverage](https://github.com/dmkopylets/cartPrice/docs/coverage/index.html)
    ![coverage.png](docs%2Fcoverage.png)  
