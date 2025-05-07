# ScaleWatch

**ScaleWatch** is a web application designed for aquaculture operations to manage daily routines and collect production data in a structured and efficient way. It enables teams to track operational tasks and record biological and technical parameters directly into structured CSV files for easy access and analysis.

[Enjoy demo here](https://scalewatch.plain-solutions.net/)

## 🚀 Features

- Daily routine execution for fish farming operations  
- Real-time data capture and logging  
- Fully configurable users, parameters and tank scopes  
- Output as structured `.csv` files for use in reporting or analysis tools  
- Lightweight Vue frontend with PHP backend  
- Works fully file-based — no database required  

## 🐟 Use Case

ScaleWatch is used in aquaculture environments (e.g., hatcheries, production units) to:

- Record biological metrics like mortality, appetite, oxygen levels, and more  
- Follow standardized checklists for daily farm routines  
- Export collected data as a CSV journal for further processing or compliance  

## 📂 Project Structure

```
/data
├── _routines/           ← Routine definitions (e.g. general.csv)
├── _config/
│   └── plant/
│       ├── params.yml   ← Parameter definitions (e.g. appetite, oxigen)
│       └── scopes.yml   ← Scope definitions (e.g. tank layouts)
├── yymmdd/              ← Collected data per day (e.g. 250507/)
│   ├── routines.csv     ← Routine inputs per day
│   └── journal.csv      ← Aggregated CSV log of all parameter inputs
```
## ⚙️ How It Works

1. **Define Routines**  
   Create CSV files in `/data/_routines/`, such as `general.csv`. Each file defines:
   - Name and interval  
   - Checklist tasks  
   - Associated parameters and scopes  

2. **Configure Users, Parameters and Scopes**  
   Edit `/data/_config/plant/users.yml`.  
   Edit `/data/_config/plant/params.yml` for parameters like `oxigen`, `mortalities`, etc.  
   Edit `/data/_config/plant/scopes.yml` for tank layout and zones.  

3. **Execute Routines**  
   On daily use, each active routine is copied into `/data/yymmdd/routines/`.  
   Inputs from users are written directly to `/data/yymmdd/journal.csv` in real time.  

4. **User Selection**  
   On initial login, a user and a routine can be selected. This can be changed with the back button.  


## 🧑‍💻 Installation

### Requirements

- PHP 8.2 or higher
- A local or remote web server (e.g., Apache, Nginx)
- Node.js & npm (for development with Vue)

### Setup

1. Clone the repository.
2. Make sure PHP is available and meets the version requirement.
3. Serve the application via your web server pointing to the project root.
4. (Optional) Install dependencies and build frontend if needed:
   ```bash
   npm install
   npm run build
   ```
5. Start using the app in your browser.

## 📁 Example Routine Definition

```CSV
name,interval,scheduled,scopes,parameters,status,user,modified,checklist
Closing Farm,daily,16:30,"n,p1,p2",appetite,,,,"Turn on feeder,Check appetite,Turn off light"
```

## 🗃 Example Parameter Configuration

```YML
appetite:
  name: Appetite
  type: number
  default: 4
  max: 4
  color: '#CC66FF'
```

## 🏭 Example Scope Configuration

```YML
p1:
  name: Production1
  gutter: 15
  rows:
    - cols:
        - name: P1 N1
          tank: p1n1
          span: 12
        - name: P1 S1
          tank: p1s1
          span: 12
    - cols:
        - name: P1 N2
          tank: p1n2
          span: 12
        - name: P1 S2
          tank: p1s2
          span: 12
```

## 📅 Output Sample (journal.csv)

```CSV
tank,mortalities,oxigen,appetite
f1,0,110,3
f2,1,105,4
```

## 🙋‍♀️ Need Help?

If you need support setting up or using ScaleWatch, feel free to reach out:
- 📧 Email: [support@plain-solutions.net]
- 🐛 Issues & Suggestions: Report bugs or suggest features via the GitHub issue tracker
- 📘 Documentation: Additional guides and configuration examples coming soon.

🧩 Contributing

ScaleWatch is an open project — contributions are welcome, whether it’s code, testing, documentation, or feedback.

To contribute:
1.	Fork the repository
2.	Create a feature branch
3.	Submit a pull request

Thank you for helping improve ScaleWatch!