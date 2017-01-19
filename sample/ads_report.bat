@echo off
setlocal EnableDelayedExpansion



set TodayYear=%date:~0,4%
set TodayMon=%date:~5,2%
set TodayDay=%date:~8,2%
if "!TodayDay!" == "08" set TodayDay=8
if "!TodayDay!" == "09" set TodayDay=9

set LastdayYear=
set LastdayMon=
set LastdayDay=

set LastdayYear=%TodayYear%
set LastdayMon=%TodayMon%
set /A LastdayDay=TodayDay-1
set /A LastdayDay2=TodayDay-2




if "%LastdayDay%" == "0" (
        if "!LastdayMon!" == "01" (
                set LastdayMon=12
                set /A LastdayYear-=1
        ) else (
                set /A LastdayMon-=1
                
                if "!LastdayMon!" == "1" set LastdayMon=01
                if "!LastdayMon!" == "2" set LastdayMon=02
                if "!LastdayMon!" == "3" set LastdayMon=03
                if "!LastdayMon!" == "4" set LastdayMon=04
                if "!LastdayMon!" == "5" set LastdayMon=05
                if "!LastdayMon!" == "6" set LastdayMon=06
                if "!LastdayMon!" == "7" set LastdayMon=07
                if "!LastdayMon!" == "8" set LastdayMon=08
                if "!LastdayMon!" == "9" set LastdayMon=09
        )

        if "!LastdayMon!" == "01" set LastdayDay=31
        if "!LastdayMon!" == "03" set LastdayDay=31
        if "!LastdayMon!" == "04" set LastdayDay=30
        if "!LastdayMon!" == "05" set LastdayDay=31
        if "!LastdayMon!" == "06" set LastdayDay=30
        if "!LastdayMon!" == "07" set LastdayDay=31
        if "!LastdayMon!" == "08" set LastdayDay=31
        if "!LastdayMon!" == "09" set LastdayDay=30
        if "!LastdayMon!" == "10" set LastdayDay=31
        if "!LastdayMon!" == "11" set LastdayDay=30
        if "!LastdayMon!" == "12" set LastdayDay=31
        
        if "!LastdayMon!" == "02" (
                set IsLeapYear=
                
                set /A IsLeapYear=!LastdayYear!%%400
                if "!IsLeapYear!" == "0" (
                        set LastdayDay=29
                        goto MAKELASTDATE
                )
                
                set /A IsLeapYear=!LastdayYear!%%100
                if "!IsLeapYear!" == "0" (
                        set LastdayDay=28
                        goto MAKELASTDATE
                )
                
                set /A IsLeapYear=!LastdayYear%%4
                if "!IsLeapYear!" == "0" (
                        set LastdayDay=29
                        goto MAKELASTDATE
                ) else (
                        set LastdayDay=28
                        goto MAKELASTDATE
                )
        )
)

:MAKELASTDATE

if "%LastdayDay%" == "1" set LastdayDay=01
if "%LastdayDay%" == "2" set LastdayDay=02
if "%LastdayDay%" == "3" set LastdayDay=03
if "%LastdayDay%" == "4" set LastdayDay=04
if "%LastdayDay%" == "5" set LastdayDay=05
if "%LastdayDay%" == "6" set LastdayDay=06
if "%LastdayDay%" == "7" set LastdayDay=07
if "%LastdayDay%" == "8" set LastdayDay=08
if "%LastdayDay%" == "9" set LastdayDay=09
set LastDate1=%LastdayYear%%LastdayMon%%LastdayDay%
set LastDate2=%LastdayYear%%LastdayMon%%LastdayDay2%

java -Xmx1G -jar aw-reporting/target/aw-reporting.jar -startDate %LastDate1% -endDate %LastDate1% -accountIdsFile d:/clone/aw-reporting/accountids.txt -file aw-reporting/src/main/resources/aw-report-sample.properties -verbose